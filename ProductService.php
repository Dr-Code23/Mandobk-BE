<?php

namespace App\Services\Api\V1\Products;

use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Http\Resources\Api\V1\Product\ProductDetails\ProductDetailsResource;
use App\Models\V1\Product;
use App\Models\V1\ProductInfo;
use App\Traits\GeneralTrait;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    use UserTrait;
    use Translatable;
    use RoleTrait;
    use GeneralTrait;

    /**
     * Undocumented function
     */
    public function fetchAllProducts(): JsonResponse
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
                    'qty',
                    'expire_date',
                    'patch_number',
                    'created_at',
                );
            }])
            ->withSum('product_details', 'qty')
            ->get();
        //return $products;
        return $this->resourceResponse(new ProductCollection($products));
    }

    /**
     * Fetch One Product With No Details
     */
    public function showOnProductWithoutDetails($product): Product|null
    {
        // return $product;
        $product = Product::where('id', $product->id)
            //? Should Use `whenLoaded` method in ProductResource To Prevent Showing Relationship
//            // ->without('product_details')
            ->where(function ($query) {
                if ($this->roleNameIn(['ceo', 'data_entry'])) {
                    $query->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']));
                } else {
                    $query->whereIn('user_id', $this->getSubUsersForUser());
                }
            })
            ->withSum('product_details', 'qty')
            ->first();
//        return $product;
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
        $limited = $request->limited ?: 0;

        // Store the barcode
        $barcode_value = $request->barcode;

        if ($this->storeBarCodeSVG('products', $barcode_value, $barcode_value)) {
            $product = Product::where(function ($query) use ($commercial_name, $barcode_value) {
                $query->where('com_name', $commercial_name)
                    ->orWhere('barcode', $barcode_value);
            })
                ->where(function ($query) {
                    if ($this->roleNameIn(['ceo', 'data_entry'])) {
                        $query->whereIn('role_id', $this->getRolesIdsByName(['ceo', 'data_entry']));
                    } else {
                        $query->whereIn('user_id', $this->getSubUsersForUser());
                    }
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
                'new_limited_value' => null,
                'role_id' => auth()->user()->role_id,
            ];
            $originalTotal = $purchase_price * $request->quantity;
            if ($product) {
                //TODO Determine If User Changed Limited Exchange
                $limitedChanged = false;

                if ($product->limited != $limited) {
                    $inputs['new_limited_value'] = $limited;
                    $limited = $product->limited;
                    $limitedChanged = true;
                }

                $originalTotal += $product->original_total;
                $inputs['original_total'] = $originalTotal;
                $product->update($inputs);

                $product->limited_changed = $limitedChanged;
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
                        'limited' => $limited,
                    ]);
            }
            $productInfo = ProductInfo::where('product_id', $product->id)
                ->where(function ($query) use ($request) {
                    $query->where('expire_date', $request->expire_date)
                        ->orWhere('patch_number', $request->patch_number);
                })
                ->first();

            if ($productInfo) {
                $productInfo->qty += $request->quantity;
                $productInfo->update();
            } else {
                $productInfo = ProductInfo::create([
                    'role_id' => Auth::user()->role_id,
                    'product_id' => $product->id,
                    'qty' => $request->quantity,
                    'expire_date' => $request->expire_date,
                    'patch_number' => $request->patch_number,
                ]);
            }
            $product->loadSum('product_details', 'qty');
            $product->detail = new ProductDetailsResource($productInfo);
            info($product);

            return $product;
        }

        // Failed To Create Or Store the barcode
        return false;
    }

    /**
     * Delete Product For User
     */
    public function destroy($product): bool
    {
        if (in_array($product->user_id, $this->getSubUsersForUser())) {
            $this->deleteBarCode($product->barcode);
            $product->delete();

            return true;
        }

        return false;
    }

    /**
     * Scientific Names For Select Box
     */
    public function ScientificNamesSelect(): Collection
    {
        return Product::where('user_id', Auth::id())
            ->get(['id', 'sc_name as scientific_name']);
    }

    /**
     * Commercial Names For Select Box
     */
    public function CommercialNamesSelect(): Collection
    {
        return Product::where('user_id', Auth::id())->get(['id', 'com_name as commercial_name']);
    }

    /**
     * Fetch ALl Products For Doctor
     */
    public function doctorProducts(): Collection
    {
        return Product::whereIn(
            'role_id',
            $this->getRolesIdsByName(['ceo', 'data_entry']),
        )->get(['id', 'com_name as commercial_name', 'limited']);
    }

    /**
     * Change Limited Exchange Status For Product
     */
    public function updateLimitedExchange(int $productId): Product|bool
    {
        $product = Product::where('id', $productId)
            ->first(['id', 'limited', 'new_limited_value', 'user_id', 'role_id']);

        if ($product) {
            if (
                in_array($product->role_id, $this->getRolesIdsByName(['ceo', 'data_entry']))
                || in_array($product->user_id, $this->getSubUsersForUser($product->user_id))
            ) {
                if (in_array($product->new_limited_value, ['0', '1'])) {
                    $confirmChanging = request('change');
                    info($confirmChanging);
                    if ($confirmChanging == 'true') {
                        $product->limited = $product->new_limited_value;
                    }
                    $product->new_limited_value = null;
                    $product->save();
                }

                return $product;
            }
        }
        // if Product Not Found
        return false;
    }
}
