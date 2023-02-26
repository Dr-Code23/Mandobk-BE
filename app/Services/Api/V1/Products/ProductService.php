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
use Illuminate\Support\Facades\Auth;
use DB;

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
                    'created_at',
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
            ->withSum('product_details', 'qty')
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
        $limited = 0;

        // Check If DataEntry Has The Product Already
        // Store the barcode
        $barcode_value = $request->barcode;
        // $adminProduct = Product::where(function ($query) use ($barcode_value, $commercial_name) {
        //     $query->where('barcode', $barcode_value)
        //         ->orWhere('com_name', $commercial_name);
        // })
        //     ->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
        //     ->first([
        //         'limited', 'sc_name', 'com_name'
        //     ]);

        // if ($adminProduct) {
        //     $commercial_name = $adminProduct->com_name;
        //     $scientific_name = $adminProduct->sc_name;
        //     $limited = $adminProduct->limited;
        // }
        if ($this->storeBarCodeSVG('products', $barcode_value, $barcode_value)) {

            $product = Product::where(function ($query) use ($commercial_name, $barcode_value) {
                $query->where('com_name', $commercial_name)
                    ->orWhere('barcode', $barcode_value);
            })
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
                'limited' => $limited,
                'user_id' => Auth::id(),
                'role_id' => $this->getAuthenticatedUserInformation()->role->id,
            ];
            $originalTotal = $purchase_price * $request->quantity;
            if ($product) {
                $originalTotal += $product->original_total;
                $inputs['original_total'] = $originalTotal;
                $product->update($inputs);
            } else {
                $inputs['original_total'] = $originalTotal;
                $product = Product::create($inputs);
            }


            // Update All Products To new Admin Values Only If Changed
            if ($this->roleNameIn(['ceo', 'data_entry'])) {
                Product::where('barcode', $barcode_value)->orWhere('com_name', $commercial_name)
                    ->whereNotIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']))
                    ->where(function ($query) use ($commercial_name, $scientific_name, $limited) {
                        $query->where('com_name', '!=', $commercial_name)
                            ->orWhere('sc_name', '!=', $scientific_name)
                            ->orWhere('limited', '!=', $limited);
                    })
                    ->update([
                        'com_name' => $commercial_name,
                        'sc_name' => $scientific_name,
                        'limited' => $limited
                    ]);
            }
            $productInfo = ProductInfo::where('product_id', $product->id)
                ->where('expire_date', $request->expire_date)
                ->where('patch_number', $request->patch_number)
                ->first();
            if ($productInfo) {
                $productInfo->qty += $request->quantity;
                $productInfo->update();
            } else $productInfo = ProductInfo::create([
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

        return Product::whereIn(
            'role_id',
            $this->getRolesIdsByName(['ceo', 'data_entry']),
        )
            ->get(['id', 'com_name as commercial_name', 'limited']);
    }

    //? For Testing Only
    public function testGetOneProduct()
    {

        $product = Product::where('id', 1)->first();

        return $this->resourceResponse(new ProductResource($product));
    }
}
