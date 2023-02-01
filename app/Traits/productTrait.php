<?php

namespace App\Traits;

use App\Http\Resources\Api\Web\V1\Product\productCollection;
use App\Http\Resources\Api\Web\V1\Product\productResource;
use App\Models\Api\Web\V1\Product;
use App\Models\Api\Web\V1\Role;
use App\Models\User;

trait productTrait
{
    use translationTrait;
    use StringTrait;
    use HttpResponse;
    use translationTrait;
    use dateTrait;
    use userTrait;
    private string $translationFileName = 'Products/productTranslationFile.';

    public function showAllProducts($products)
    {
        return $this->resourceResponse(new productCollection($products));
    }

    public function showOneProduct($product)
    {
        return $this->resourceResponse(new productResource($product));
    }

    public function storeProduct($request)
    {
        // Get Authenticated user information
        $authenticatedUserInformation = $this->getAuthenticatedUserInformation();
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $provider = $this->sanitizeString($request->provider);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check if either commercial name or scientefic_name exists
        $com_exists = false;
        $sc_exists = false;
        $role_name_for_user = Role::where('id', $authenticatedUserInformation->role_id)->first(['name'])->name;
        if (
            Product::where(function ($bind) use ($commercial_name, $role_name_for_user) {
                $bind->where('com_name', $commercial_name);
                $bind->where('role_id', $this->getAuthenticatedUserInformation()->role_id);
                if (!in_array($role_name_for_user, config('roles.admin_product_role'))) {
                    $bind->where('user_id', $this->getAuthenticatedUserId());
                }
            })->first(['id'])) {
            $com_exists = true;
        }
        if (
            Product::where(function ($bind) use ($scientefic_name, $role_name_for_user) {
                $bind->where('sc_name', $scientefic_name);
                $bind->where('role_id', $this->getAuthenticatedUserInformation()->role_id);
                if (!in_array($role_name_for_user, config('roles.admin_product_role'))) {
                    $bind->where('user_id', $this->getAuthenticatedUserId());
                }
            })->first(['id'])) {
            $sc_exists = true;
        }
        if (!$com_exists && !$sc_exists) {
            /* Make the barcode for the product */
            // Generate A Barcode for the product
            $random_number = rand(1, 1000000000);
            while (file_exists(asset('storage/data_entry/'.$random_number.'.svg'))) {
                $random_number = rand(1, 1000000000);
            }
            // Store the barcode
            $barcode_value = $random_number;
            if ($this->storeBarCodeSVG('data_entry', $random_number, $barcode_value)) {
                $data_entry = Product::create([
                    'com_name' => $commercial_name,
                    'sc_name' => $scientefic_name,
                    'qty' => $request->quantity,
                    'pur_price' => $purchase_price,
                    'sel_price' => $selling_price,
                    'bonus' => $bonus,
                    'con' => $concentrate,
                    'patch_number' => $request->patch_number,
                    'bar_code' => $barcode_value,
                    'provider' => $provider,
                    'limited' => $request->limited ? 1 : 0,
                    'user_id' => $authenticatedUserInformation->id,
                    'role_id' => $authenticatedUserInformation->role_id,
                    'entry_date' => $request->entry_date,
                    'expire_date' => $request->expire_date,
                ]);

                return $this->success(new productResource($data_entry), 'Product Created Successfully');
            }

            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientefic_name exists

        $payload = [];
        if ($com_exists) {
            $payload['commercial_name'] = [$this->translateErrorMessage($this->translationFileName.'commercial_name', 'unique')];
        }
        if ($sc_exists) {
            $payload['scientefic_name'] = [$this->translateErrorMessage($this->translationFileName.'scientefic_name', 'unique')];
        }

        return $this->validation_errors($payload);
    }

    public function updateProduct($request, $product)
    {
        $authenticatedUserInformation = $this->getAuthenticatedUserInformation();
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $provider = $this->sanitizeString($request->provider);
        $purchase_price = $this->setPercisionForFloatString($request->purchase_price);
        $selling_price = $this->setPercisionForFloatString($request->selling_price);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $concentrate = $this->setPercisionForFloatString($request->concentrate);

        // Check if either commercial name or scientefic_name exists
        $com_exists = false;
        $sc_exists = false;

        $role_name_for_user = Role::where('id', $authenticatedUserInformation->role_id)->first(['name'])->name;
        if (
            Product::where(function ($bind) use ($commercial_name, $role_name_for_user, $product) {
                $bind->where('com_name', $commercial_name);
                $bind->where('role_id', $this->getAuthenticatedUserInformation()->role_id);
                if (!in_array($role_name_for_user, config('roles.admin_product_role'))) {
                    $bind->where('user_id', $this->getAuthenticatedUserId());
                }
                $bind->where('id', '!=', $product->id);
            })->first(['id'])) {
            $com_exists = true;
        }
        // New
        if (
            Product::where(function ($bind) use ($scientefic_name, $role_name_for_user, $product) {
                $bind->where('sc_name', $scientefic_name);
                $bind->where('role_id', $this->getAuthenticatedUserInformation()->role_id);
                if (!in_array($role_name_for_user, config('roles.admin_product_role'))) {
                    $bind->where('user_id', $this->getAuthenticatedUserId());
                }
                $bind->where('id', '!=', $product->id);
            })->first(['id'])) {
            $sc_exists = true;
        }
        if (!$com_exists && !$sc_exists) {
            $random_number = null;
            $barCodeStored = false;
            $barCodeValue = null;
            $anyChangeOccured = false;
            // Check if $generate_another_bar_code Variable isset to generate another barcode
            if ($request->has('generate_another_bar_code') && $request->input('generate_another_bar_code') == true) {
                // Delete The Old Barcode
                $this->deleteBarCode($product->bar_code);

                // Generate A Barcode for the product
                $random_number = rand(1, 1000000000);
                while (file_exists(asset('storage/data_entry/'.$random_number.'.svg'))) {
                    $random_number = rand(1, 1000000000);
                }
                // Store the barcode
                $barCodeValue = $random_number;
                $barCodeStored = $this->storeBarCodeSVG('data_entry', $random_number, $barCodeValue);
                $anyChangeOccured = true;
            }

            // Begin Update Logic If Any Change Occured
            if ($product->com_name != $commercial_name) {
                $product->com_name = $commercial_name;
                $anyChangeOccured = true;
            }
            if ($product->sc_name != $scientefic_name) {
                $product->sc_name = $scientefic_name;
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
            if ($product->provider != $provider) {
                $product->provider = $provider;
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
            if (($random_number && $barCodeStored) || !$random_number) {
                if ($random_number) {
                    $product->bar_code = $barCodeValue;
                    $anyChangeOccured = true;
                }
                if ($anyChangeOccured) {
                    $product->update();

                    return $this->success(new productResource($product), 'Product Updated Successfully');
                }

                return $this->noContentResponse();
            }
            // Failed To Create Or Store the barcode
            return $this->error(null, 500, 'Failed To Create Barcode');
        }

        // Either commercial Name or scientefic_name exists

        $payload = [];
        if ($com_exists) {
            $payload['commercial_name'] = [$this->translateErrorMessage($this->translationFileName.'commercial_name', 'unique')];
        }
        if ($sc_exists) {
            $payload['scientefic_name'] = [$this->translateErrorMessage($this->translationFileName.'scientefic_name', 'unique')];
        }

        return $this->validation_errors($payload);
    }

    public function deleteProduct($product)
    {
        $this->deleteBarCode($product->bar_code);
        $product->delete();

        return $this->success(null, 'Product Deleted Successfully');
    }
}
