<?php

namespace App\Repository;

use App\Http\Resources\Api\V1\Site\Sales\SaleCollection;
use App\Http\Resources\Api\V1\Site\Sales\SaleResource;
use App\Models\User;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Models\V1\Sale;
use App\RepositoryInterface\SalesRepositoryInterface;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class DBSalesRepository implements SalesRepositoryInterface
{
    use UserTrait;
    use Translatable;
    use HttpResponse;
    use RoleTrait;

    /**
     * @return mixed
     */
    public function getAllSales()
    {
        $sales = Sale::join('users', 'sales.to_id', 'users.id')
            ->whereIn('sales.from_id', $this->getSubUsersForAuthenticatedUser())
            ->get([
                'sales.id as id',
                'sales.from_id as from_id',
                'sales.to_id as to_id',
                'users.full_name as full_name',
                'sales.details as details',
                'sales.created_at as created_at',
                'sales.updated_at as updated_at',
            ]);

        return $this->resourceResponse(new SaleCollection($sales));
    }

    /**
     * @param mixed $request
     *
     * @return mixed
     */
    public function storeSale($request)
    {
        $products = $request->data;
        $uniqueProducts = [];
        $errors = [];

        // ! Cannot Append Values in foreach
        $products_count = count($products);

        for ($i = 0; $i < $products_count; ++$i) {
            $products[$i]['product_exists'] = Product::where('id', $products[$i]['product_id'])
                ->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())
                ->first(['id'])
                ? true : false;
        }

        for ($i = 0; $i <= $products_count; ++$i) {
            // * Remove The Product from the cart if the product does not exists
            if (isset($products[$i]['product_exists']) && !$products[$i]['product_exists']) {
                unset($products[$i]);
                --$products_count;
            }
            unset($products[$i]['product_exists']);
        }

        for ($i = 0; $i < $products_count; ++$i) {
            $productInfo = Product::where('id', $products[$i]['product_id'])
                ->withSum(['product_details' => function ($query) {
                    $query->where('expire_date', '>', date('Y-m-d'))
                        ->where('qty', '>', 0);
                }], 'qty')
                ->first([
                    'com_name',
                    'sc_name',
                    'con',
                    'pur_price',
                    'sel_price',
                ]);
            $productId = $products[$i]['product_id'];

            // Check if the product is already added
            if (isset($uniqueProducts[$productId])) {
                $products[$i]['selling_price'] = $uniqueProducts[$productId]['selling_price'];
                $total_product_quantity = $products[$i]['quantity'] + $uniqueProducts[$productId]['quantity'];
                $products[$i]['quantity'] = $total_product_quantity;
                $uniqueProducts[$productId]['quantity'] = $total_product_quantity;
            } else {
                $uniqueProducts[$productId]['selling_price'] = $products[$i]['selling_price'];
                $uniqueProducts[$productId]['quantity'] = $products[$i]['quantity'];
            }
            if (!isset($uniqueProducts[$productId]['commercial_name'])) {
                $uniqueProducts[$productId]['commercial_name'] = $productInfo->com_name;
                $uniqueProducts[$productId]['scientific_name'] = $productInfo->sc_name;
                $uniqueProducts[$productId]['purchase_price'] = $productInfo->pur_price;
                $uniqueProducts[$productId]['original_qty'] = $productInfo->product_details_sum_qty;
            }
        }

        $cnt = 0;
        foreach ($uniqueProducts as $productId => $productInfo) {
            if ($productInfo['original_qty'] < $productInfo['quantity']) {
                $errors[$cnt]['quantity'][] = $this->translateErrorMessage('quantity', 'quantity.big') . $productInfo['original_qty'];
            }
            ++$cnt;
        }

        // ? Send To Who ?
        $buyerId = null;
        $loggedUserRole = $this->getRoleNameForAuthenticatedUser();
        if ($this->roleNameIn(['pharmacy', 'pharmacy_sub_user'])) {
            $buyerId = User::where('username', 'customer')->value('id');
        } elseif ($loggedUserRole == 'company') {
            $buyerId = User::where('id', $request->buyer_id)
                ->whereIn('role_id', $this->getRolesIdsByName(['storehouse']))->value('id');
        } elseif ($loggedUserRole == 'storehouse') {
            $buyerId = User::where('id', $request->buyer_id)
                ->whereIn('role_id', $this->getRolesIdsByName(['pharmacy']))
                ->value('id');
        }

        if (!$uniqueProducts) {
            $errors['product'] = 'Choose at least one existing product';
        }
        if (!$buyerId) {
            $errors['buyer_id'] = $this->translateErrorMessage('the_buyer', 'not_exists');
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }

        $totalSales = 0;
        $productsIds = array_keys($uniqueProducts);
        $sentProducts = [];
        for ($i = 0; $i < count($productsIds); ++$i) {
            $productId = $productsIds[$i];
            $productInfo = $uniqueProducts[$productId];

            $allProductDetails = ProductInfo::where('product_id', $productId)
                ->orderByDesc('qty')
                ->get(['id', 'qty']);

            $temporaryQty = (int) $productInfo['quantity'];
            $detailsCount = count($allProductDetails);
            $index = 0;

            // Decrease The Original Quantity
            while ($temporaryQty && $index < $detailsCount) {
                $detail = $allProductDetails[$i];
                if ($detail->qty >= $temporaryQty) {
                    $detail->qty -= $temporaryQty;
                    $temporaryQty = 0;
                } else {
                    $temporaryQty -= $detail->qty;
                    $detail->qty = 0;
                }
                $detail->update();
                ++$index;
            }

            $totalSales += ($productInfo['selling_price'] * $productInfo['quantity']);
            unset($productInfo['original_qty']);
            $sentProducts[] = $productInfo;
        }

        $sale = Sale::create([
            'from_id' => Auth::id(),
            'to_id' => $buyerId,
            'details' => $sentProducts,
            'total' => $totalSales,
        ]);

        $sale->full_name = User::where('id', $buyerId)->value('full_name');

        return $this->createdResponse(new SaleResource($sale), $this->translateSuccessMessage('product', 'created'));
    }
}
