<?php

namespace App\Repository;

use App\Models\Api\Web\V1\Product;
use App\Models\Api\Web\V1\Role;
use App\Models\Api\Web\V1\Sale;
use App\Models\Api\Web\V1\SubUser;
use App\Models\User;
use App\RepositoryInterface\SalesRepositoryInterface;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Validator;

class DBSalesRepository implements SalesRepositoryInterface
{
    use userTrait;
    use translationTrait;
    use HttpResponse;

    /**
     * @return mixed
     */
    public function getAllSales(int $type)
    {
        $pharmacy_role = Role::where('name', 'pharmacy')->first(['id'])->id;
        $pharmacy_sub_user_role = Role::where('name', 'pharmacy_sub_user')->first(['id'])->id;

        // Then it's Company To Storehouse
        $sales = Sale::join('users', function ($join) use ($pharmacy_role, $pharmacy_sub_user_role, $type) {
            $join->on('users.id', 'sales.to_id')
                    ->where(function ($query) use ($pharmacy_role, $pharmacy_sub_user_role, $type) {
                        // From Company To Storehouse
                        if ($type == 1) {
                            $query->where('users.role_id', Role::where('name', 'storehouse')->first(['id'])->id);
                        }
                        // From Storehouse To Pharmacy (Admin or sub_user)
                        elseif ($type == 2) {
                            $query->where('users.role_id', '=', $pharmacy_role)
                                ->orWhere('users.role_id', '=', $pharmacy_sub_user_role);
                        }
                        // From Pharmacy(Admin or sub_user) to random customer
                        elseif ($type == 3) {
                            $query->where('users.role_id', '=', Role::where('name', 'customer')->first(['id'])->id);
                        } else {
                            $query->where('users.role_id', '=', 1000); // Impossible to occur
                        }
                    }
                    );
        })
        ->where(function ($query) use ($pharmacy_sub_user_role, $pharmacy_role) {
            $authenticated_user_role_id = $this->getAuthenticatedUserInformation()->role_id;
            if (in_array($authenticated_user_role_id, [$pharmacy_role, $pharmacy_sub_user_role])) {
                $sub_users = [];
                $parent_id = SubUser::where('sub_user_id', $this->getAuthenticatedUserId())->first(['parent_id']);
                // if logged user is pharmacy check sales made by pharmacy sub users as well
                $query->where('users.from_id', $this->getAuthenticatedUserId());
                if ($parent_id) {
                    foreach (SubUser::where('parent_id', $parent_id)->get(['sub_user_id']) as $sub_user_id) {
                        $sub_users[] = $sub_user_id->sub_user_id;
                    }
                }
                if ($sub_users) {
                    $query->orWhereIn('users.from_id', $sub_users);
                }
            }
        })
        ->get([
            'sales.id as id',
            'sales.from_id as from_id',
            'sales.to_id as to_id',
            'users.full_name as full_name',
            'sales.details as details',
            'sales.created_at as created_at',
            'sales.updated_at as updated_at',
        ]);

        return $sales;
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
        $data = $request->input('data');

        if (!is_array($data)) {
            return $this->validation_errors([
                'Data is not array',
            ]);
        }
        $errors = [];
        if ($data) {
            foreach ($data as $product_information) {
                $validator = Validator::make($product_information, $rules, $messages);
                if ($validator->fails()) {
                    $errors['key_'.$cnt] = $validator->errors();
                }
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

        $cnt = 0;
        // return $data;
        // ! Cannot Append Values in foreach
        $data_count = count($data);

        for ($i = 0; $i < $data_count; ++$i) {
            $data[$i]['product_exists'] = Product::where('id', $data[$i]['product_id'])
                    ->where('user_id', $this->getAuthenticatedUserId())
                    ->first(['id'])
                    ? true : false;
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }

        $data_length = count($data);
        for ($i = 0; $i < $data_length; ++$i) {
            // * Remove The Product from the cart if the product does not exists
            if (!$data[$i]['product_exists']) {
                unset($data[$i]);
                --$data_length;
            }
        }

        // Validate Quantity
        for ($i = 0; $i < count($data); ++$i) {
            $product_info = Product::where('id', $data[$i]['product_id'])->first([
                'com_name',
                'sc_name',
                'con',
                'pur_price',
                'sel_price',
                'qty',
            ]);
            if ($data[$i]['quantity'] > $product_info->qty) {
                $errors['product_id_'.$data[$i]['product_id']] = 'Quantity is bigger than existing quantity which is '.$product_info->qty;
            } else {
                $data[$i]['com_name'] = $product_info->com_name; // Commercial Name
                $data[$i]['sc_name'] = $product_info->sc_name;
                $data[$i]['pur_price'] = $product_info->pur_price;
            }
        }

        // ? Send To Who ?
        $send_to_id = null;
        if ($request->routeIs('company-sales-add')) {
            $storehouse_id = $request->input('storehouse_id');
            if ($storehouse_id && is_numeric($storehouse_id)) {
                if ($storehouse_id = User::where('id', $storehouse_id)->where('role_id', Role::where('name', 'storehouse')->first(['id'])->id)->first(['id'])) {
                    $send_to_id = $storehouse_id->id;
                } else {
                    $errors['storehouse_id'] = 'Storehouse Not Exists';
                }
            } else {
                $errors['store_house'] = 'StoreHouse is invalid';
            }
        } elseif ($request->routeIs('storehouse-sales-add')) {
            $pharmacy_id = $request->input('pharmacy_id');
            if ($pharmacy_id && is_numeric($pharmacy_id)) {
                if ($pharmacy_id = User::where('id', $pharmacy_id)->first(['id'])) {
                    $send_to_id = $pharmacy_id->id;
                } else {
                    $errors['pharmacy_id'] = 'Pharmacy id do not exists';
                }
            } else {
                $errors['pharmacy_id'] = 'Pharmacy is invalid';
            }
        } elseif ($request->routeIs('pharmacy-sales-add')) {
            $send_to_id = User::where('username', 'customer')->first(['id'])->id;
        }
        if ($errors) {
            return $this->validation_errors($errors);
        }
        if ($send_to_id) {
            // Start To Store Sale
            $sale = Sale::create([
                'from_id' => $this->getAuthenticatedUserId(),
                'to_id' => $send_to_id,
                'details' => json_encode($data),
            ]);

            return $this->success($sale, 'Sale Created Successfully');
        }
        // Now Everything is valid , start to store
        // foreach($data)
    }

    private function addSales($products)
    {
    }
}
