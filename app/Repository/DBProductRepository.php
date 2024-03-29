<?php

namespace App\Repository;

use App\Http\Requests\Api\V1\Product\ProductRequest;
use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\DateTrait;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DBProductRepository implements ProductRepositoryInterface
{
    use HttpResponse;
    use UserTrait;
    use Translatable;
    use DateTrait;
    use RoleTrait;

    public function showAllProducts()
    {
        $products = Product::where(function ($query) {
            if (! $this->roleNameIn(['ceo', 'data_entry'])) {
                $query->whereIn('products.user_id', $this->getSubUsersForUser());
            }
        })
            ->with(['product_details' => function ($query) {
                $query->select(
                    'id',
                    'role_id',
                    'product_id',
                    'qty as quantity',
                    'expire_date',
                    'patch_number',
                    'created_at'
                );
            }])
            ->get();

        return $this->resourceResponse(new ProductCollection($products));
    }

    /**
     * @return mixed
     */
    public function showOnProductWithoutDetails($product)
    {
        $product = Product::where('id', $product->id)
            //? Should Use `whenLoaded` method in ProductResource To Prevent Showing Relationship
            // ->without('product_details')
            ->whereIn('user_id', $this->getSubUsersForUser())
            ->first();

        if ($product) {
            return $this->resourceResponse(new ProductResource($product));
        }

        return $this->notFoundResponse($this->translateSuccessMessage('product', 'not_found'));
    }

    public function showOneProductWithDetails($product)
    {
        $product = Product::where('products.id', $product->id)
            ->where(function ($query) {
                if (! $this->roleNameIn(['ceo', 'data_entry'])) {
                    $query->whereIn('products.user_id', $this->getSubUsersForUser());
                }
            })
            ->with(['product_details' => function ($query) {
                $query->select(
                    'product_id',
                    'qty as quantity',
                    'expire_date',
                    'patch_number',
                    'created_at'
                );
            }])
            ->first();

        return $this->resourceResponse(new ProductResource($product));
    }

    /**
     * Store Product.
     *
     * @param  ProductRequest  $request
     * @return JsonResponse
     */
    public function storeProduct($request)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientific_name = $this->sanitizeString($request->scientific_name);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check if either commercial name or scientific_name exists
        $product_exists = false;
        if (
            Product::where(function ($bind) use ($commercial_name, $scientific_name, $concentrate) {
                $bind->where('com_name', $commercial_name);
                $bind->where('sc_name', $scientific_name);
                $bind->where('con', $concentrate);
                $bind->whereIn('user_id', $this->getSubUsersForUser());
            })->first(['id'])
        ) {
            $product_exists = true;
        }

        if (! $product_exists) {
            // Check if the admin has already added the product

            // Check If Data Entry Has has product
            $admin_product = Product::where('com_name', $commercial_name)
                ->where('sc_name', $scientific_name)
                ->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
                ->first(['limited']);

            /* Make the barcode for the product */
            // Generate A Barcode for the product
            $barcode = rand(1, 100000);
            // Store the barcode
            $barcode_value = $barcode;

            if ($this->storeBarCodeSVG('products', $barcode, $barcode_value)) {
                $product = Product::create([
                    'com_name' => $commercial_name,
                    'sc_name' => $scientific_name,
                    'qty' => $request->quantity,
                    'pur_price' => $purchase_price,
                    'sel_price' => $selling_price,
                    'bonus' => $bonus,
                    'con' => $concentrate,
                    'patch_number' => $request->patch_number,
                    'barcode' => $barcode_value,
                    'original_total' => $purchase_price * $request->quantity,
                    'limited' => $admin_product ? $admin_product->limited : ($request->limited ? 1 : 0),
                    'user_id' => Auth::id(),
                    'role_id' => Auth::user()->role_id,
                    'entry_date' => $request->entry_date,
                    'expire_date' => $request->expire_date,
                ]);

                return $this->success(new ProductResource($product), $this->translateSuccessMessage('product', 'created'));
            }

            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientific_name exists

        $payload = [];
        if ($product_exists) {
            $payload['product'] = $this->translateErrorMessage('product', 'exists');
        }

        return $this->validationErrorsResponse($payload);
    }

    /**
     * @param  mixed  $request
     * @return mixed
     */
    public function updateProduct($request, $product)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientific_name = $this->sanitizeString($request->scientific_name);
        $provider = $this->sanitizeString($request->provider);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        $admin_roles = [
            $this->getRoleIdByName('ceo'),
            $this->getRoleIdByName('data_entry'),
        ];
        // Check if either commercial name or scientific_name exists
        $product_exists = false;
        if (
            Product::where(function ($bind) use ($commercial_name, $scientific_name, $concentrate, $product) {
                $bind->where('com_name', $commercial_name);
                $bind->where('sc_name', $scientific_name);
                $bind->where('con', $concentrate);
                $bind->whereIn('user_id', $this->getSubUsersForUser());
                $bind->where('id', '!=', $product->id);
            })->first(['id'])
        ) {
            $product_exists = true;
        }

        if (! $product_exists) {
            $random_number = null;
            $barCodeStored = false;
            $barCodeValue = null;
            $anyChangeOccured = false;

            // Begin Update Logic If Any Change Occured
            if ($product->com_name != $commercial_name) {
                $product->com_name = $commercial_name;
                $anyChangeOccured = true;
            }
            if ($product->sc_name != $scientific_name) {
                $product->sc_name = $scientific_name;
                $anyChangeOccured = true;
            }
            if ($product->qty != $request->quantity) {
                $product->qty = $request->quantity;
                $anyChangeOccured = true;
            }
            if ($product->pur_price != $purchase_price) {
                $product->pur_price = $purchase_price;
                $anyChangeOccured = true;
            }
            if ($product->sel_price != $selling_price) {
                $product->sel_price = $selling_price;
                $anyChangeOccured = true;
            }
            if ($product->bonus != $bonus) {
                $product->bonus = $bonus;
                $anyChangeOccured = true;
            }
            if ($product->con != $concentrate) {
                $product->con = $concentrate;
                $anyChangeOccured = true;
            }
            if ($product->patch_number != $request->patch_number) {
                $product->patch_number = $request->patch_number;
                $anyChangeOccured = true;
            }
            if ($product->limited != (int) $request->limited) {
                $product->limited = $request->limited ? 1 : 0;
                $anyChangeOccured = true;
            }
            if ($this->changeDateFormat($product->entry_date, 'Y-m-d') != $request->entry_date) {
                $product->entry_date = $request->entry_date;
                $anyChangeOccured = true;
            }
            if ($this->changeDateFormat($product->expire_date, 'Y-m-d') != $request->expire_date) {
                $product->expire_date = $request->expire_date;
                $anyChangeOccured = true;
            }
            if (($random_number && $barCodeStored) || ! $random_number) {
                if ($random_number) {
                    $product->barcode = $barCodeValue;
                    $anyChangeOccured = true;
                }
                if ($anyChangeOccured) {
                    $product->update();

                    return $this->success(new ProductResource($product), 'Product Updated Successfully');
                }

                return $this->noContentResponse();
            }
            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientific_name exists

        $payload = [];
        if ($product_exists) {
            $payload['product_exists'] = $this->translateErrorMessage('product', 'exists');
        }

        return $this->validationErrorsResponse($payload);
    }

    /**
     * Summary of deleteProduct.
     *
     * @param  mixed  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct($product)
    {
        if (in_array($product->user_id, $this->getSubUsersForUser())) {
            $this->deleteBarCode($product->barcode);
            $product->delete();

            return $this->success(null, 'Product Deleted Successfully');
        }

        return $this->notFoundResponse($this->translateErrorMessage('product', 'not_exists'));
    }
}
