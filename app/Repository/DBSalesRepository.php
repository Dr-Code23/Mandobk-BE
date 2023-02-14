<?php

namespace App\Repository;

use App\Http\Resources\Api\V1\Site\Sales\SaleCollection;
use App\Http\Resources\Api\V1\Site\Sales\SaleResource;
use App\Models\User;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\Models\V1\Sale;
use App\RepositoryInterface\SalesRepositoryInterface;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $rules = [
            'product_id' => ['required'],
            'expire_date' => ['required', 'date_format:Y-m-d'],
            'selling_price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ];
        $messages = [
            'product_id.required' => $this->translateErrorMessage('product', 'required'),
            'expire_date.required' => $this->translateErrorMessage('expire_date', 'required'),
            'expire_date.date_format' => $this->translateErrorMessage('expire_date', 'date_format'),
            'selling_price.required' => $this->translateErrorMessage('selling_price', 'required'),
            'selling_price.numeric' => $this->translateErrorMessage('selling_price', 'numeric'),
            'selling_price.min' => $this->translateErrorMessage('selling_price', 'min.numeric'),
            'quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
        ];
        $cnt = 0;
        $products = $request->input('data');
        $uniqueProducts = [];

        if (!is_array($products)) {
            return $this->validation_errors([
                'Data is not array',
            ]);
        }
        $errors = [];
        if ($products) {
            foreach ($products as $product_information) {
                $validator = Validator::make($product_information, $rules, $messages);
                if ($validator->fails()) {
                    $errors[$cnt] = $validator->errors();
                }
                $errors[$cnt] = $validator->fails() ? $validator->errors() : ['error' => false];
                ++$cnt;
            }
        } else {
            // if Request all is empty
            $errors['product'] = $this->translateErrorMessage('product', 'required');

            return $this->validation_errors($errors);
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }

        // ! Cannot Append Values in foreach
        $products_count = count($products);

        for ($i = 0; $i < $products_count; ++$i) {
            $products[$i]['product_exists'] = Product::where('id', $products[$i]['product_id'])
            ->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())
            ->first(['id'])
            ? true : false;
        }

        if ($errors) {
            return $this->validation_errors($errors);
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
            $productInfo = Product::where('id', $products[$i]['product_id'])->first([
                'com_name',
                'sc_name',
                'con',
                'pur_price',
                'sel_price',
                'qty',
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
                $uniqueProducts[$productId]['original_qty'] = $productInfo->qty;
            }
        }

        foreach ($uniqueProducts as $productId => $product_info) {
            if ($product_info['original_qty'] < $product_info['quantity']) {
                $errors[$productId]['quantity'][] = $this->translateErrorMessage('quantity', 'big');
            }
        }

        if ($errors) {
            return $this->validation_errors($errors);
        }
        // return $errors;
        // ? Send To Who ?
        $sendToId = null;
        if ($request->routeIs('company-sales-add')) {
            $storehouse_id = $request->input('storehouse_id');
            if ($storehouse_id && is_numeric($storehouse_id)) {
                if ($storehouse_id = User::where('id', $storehouse_id)
                    ->where('role_id', Role::where('name', 'storehouse')->value('id'))->first(['id'])
                ) {
                    $sendToId = $storehouse_id->id;
                } else {
                    $errors['storehouse_id'] = 'Storehouse Not Exists';
                }
            } else {
                $errors['store_house'] = 'StoreHouse is invalid';
            }
        } elseif ($request->routeIs('storehouse-sales-add')) {
            $pharmacy_id = $request->input('pharmacy_id');
            if ($pharmacy_id && is_numeric($pharmacy_id)) {
                if ($pharmacy_id = User::where('id', $pharmacy_id)
                    ->where('role_id', Role::where('name', 'pharmacy')->value('id'))
                    ->first(['id'])
                ) {
                    $sendToId = $pharmacy_id->id;
                } else {
                    $errors['pharmacy_id'] = 'Pharmacy id do not exists';
                }
            } else {
                $errors['pharmacy_id'] = 'Pharmacy is invalid';
            }
        } elseif ($request->routeIs('pharmacy-sales-add')) {
            $sendToId = User::where('username', 'customer')->value('id');
        }
        if (!$products) {
            $errors['product'] = 'Choose at least one existing product';
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }

        if ($sendToId) {
            $totalSales = 0;
            $productsIds = array_keys($uniqueProducts);
            $sentProducts = [];
            for ($i = 0; $i < count($productsIds); ++$i) {
                $productId = $productsIds[$i];
                $productInfo = $uniqueProducts[$productId];
                Product::where('id', $productId)->update(
                    [
                        'qty' => ((int) $productInfo['original_qty'] - (int) $productInfo['quantity']),
                    ]
                );
                $totalSales += ($productInfo['selling_price'] + $productInfo['quantity']);
                unset($productInfo['original_qty']);
                $sentProducts[] = $productInfo;
            }

            $sale = Sale::create([
                'from_id' => Auth::id(),
                'to_id' => $sendToId,
                'details' => $sentProducts,
                'total' => $totalSales,
            ]);

            $sale->full_name = User::where('id', $sendToId)->value('full_name');

            return $this->createdResponse(new SaleResource($sale), $this->translateSuccessMessage('product', 'created'));
        }
    }
}
