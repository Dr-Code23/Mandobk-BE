<?php

namespace App\Services\Api\V1\Products;

use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Auth;

class ProductService
{

    use UserTrait;
    use Translatable;
    use RoleTrait;

    /**
     * Fetch One Product With No Details
     *
     * @param $product
     * @return Product|null
     */
    public function showOnProductWithoutDetails($product): Product|null
    {
        $product = Product::where('id', $product->id)
            //? Should Use `whenLoaded` method in ProductResource To Prevent Showing Relationship
            // ->without('product_details')
            ->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())
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
        $admin_product = Product::where('com_name', $commercial_name)
            ->where('sc_name', $scientific_name)
            ->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
            ->first(['limited']);

        /* Make the barcode for the product */

        // Store the barcode
        $barcode_value = $request->barcode;

        if ($this->storeBarCodeSVG('products', $barcode_value, $barcode_value)) {

            $product = Product::where('com_name', $commercial_name)
                ->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())->first();

            $inputs = [
                'com_name' => $commercial_name,
                'sc_name' => $scientific_name,
                'pur_price' => $purchase_price,
                'sel_price' => $selling_price,
                'bonus' => $bonus,
                'con' => $concentrate,
                'barcode' => $barcode_value,
                'original_total' => $purchase_price * $request->quantity,
                'limited' => $admin_product ? $admin_product->limited : ($request->limited ? 1 : 0),
                'user_id' => Auth::id(),
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

            unset($productInfo->updated_at);
            unset($productInfo->role_id);
            $product->detail = $productInfo;
            return $product;
        }

        // Failed To Create Or Store the barcode
        return false;

        // Either commercial Name or scientific_name exists

    }
}
