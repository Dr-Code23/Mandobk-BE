<?php

namespace App\Services\Api\V1\Products;

use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Http\Resources\Api\V1\Product\ProductDetails\ProductDetailsResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\GeneralTrait;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Auth;

class ProductService
{

    use UserTrait;
    use Translatable;
    use RoleTrait;
    use GeneralTrait;


    /**
     * Undocumented function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchAllProducts()
    {

        $products = Product::where(function ($query) {
            if (!$this->roleNameIn(['ceo', 'data_entry']))
                $query->whereIn('products.user_id', $this->getSubUsersForAuthenticatedUser());
        })
            ->with(['product_details' => function ($query) {
                $query->select(
                    'id',
                    'role_id',
                    'product_id',
                    'qty',
                    'expire_date',
                    'patch_number',
                    'created_at'
                );
            }])
            ->get();

        return $this->resourceResponse(new ProductCollection($products));
    }
    /**
     * Fetch One Product With No Details
     *
     * @param $product
     * @return Product|null
     */
    public function showOnProductWithoutDetails($product): Product|null
    {
        // return $product;
        $product = Product::where('id', $product->id)
            //? Should Use `whenLoaded` method in ProductResource To Prevent Showing Relationship
            // ->without('product_details')
            ->where(function ($query) {
                if ($this->roleNameIn(['ceo', 'data_entry'])) $query->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']));
                else  $query->whereIn('user_id', $this->getSubUsersForAuthenticatedUser());
            })
            ->first();

        if ($product) {
            return $product;
        }
        return null;
    }

    public function storeOrUpdate($request)
    {

        $commercial_name = $request->commercial_name;
        $scientific_name = $request->scientific_name;
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check If Data Entry Has has product
        // $admin_product = Product::where('com_name', $commercial_name)
        //     ->where('sc_name', $scientific_name)
        //     ->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
        //     ->first(['limited']);

        /* Make the barcode for the product */

        // Store the barcode
        $barcode_value = $request->barcode;

        if ($this->storeBarCodeSVG('products', $barcode_value, $barcode_value)) {

            $product = Product::where('com_name', $commercial_name)
                ->where(function ($query) {
                    if ($this->roleNameIn(['ceo', 'data_entry'])) $query->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']));
                    else  $query->whereIn('user_id', $this->getSubUsersForAuthenticatedUser());
                })
                ->first();

            $inputs = [
                'com_name' => $commercial_name,
                'sc_name' => $scientific_name,
                'pur_price' => $purchase_price,
                'sel_price' => $selling_price,
                'bonus' => $bonus,
                'con' => $concentrate,
                'barcode' => $barcode_value,
                'original_total' => $purchase_price * $request->quantity,
                'limited' => $request->limited ? 1 : 0,
                'user_id' => Auth::id(),
                'role_id' => $this->getAuthenticatedUserInformation()->role->id,
            ];
            if ($product) $product->update($inputs);
            else $product = Product::create($inputs);


            $productInfo = ProductInfo::updateOrCreate([
                'product_id' => $product->id,
                'expire_date' => $request->expire_date,
                'patch_number' => $request->patch_number
            ], [
                'role_id' => Auth::user()->role_id,
                'product_id' => $product->id,
                'qty' => $request->quantity,
                'expire_date' => $request->expire_date,
                'patch_number' => $request->patch_number
            ]);

            $product->detail = new ProductDetailsResource($productInfo);
            return $product;
        }

        // Failed To Create Or Store the barcode
        return false;

        // Either commercial Name or scientific_name exists

    }


    public function ScientificNamesSelect()
    {
        return Product::where('user_id', Auth::id())
            ->get(['id', 'sc_name as scientific_name']);
    }

    public function CommercialNamesSelect()
    {
        return Product::where('user_id', Auth::id())->get(['id', 'com_name as commercial_name']);
    }

    public function doctorProducts()
    {

        return $this->resourceResponse(Product::whereIn(
            'role_id',
            $this->getRolesIdsByName(['ceo', 'data_entry']),
        )
            ->get(['id', 'sc_name as scientific_name', 'limited']));
    }

    //? For Testing Only
    public function testGetOneProduct()
    {

        $product = Product::where('id', 1)->first();

        return $this->resourceResponse(new ProductResource($product));
    }
}
