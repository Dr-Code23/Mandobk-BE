<?php

namespace App\Traits;

use App\Models\Api\Web\V1\Role;
use App\Models\Api\Web\V1\Sale;
use App\Models\Api\Web\V1\SubUser;

trait salesTrait
{
    use userTrait;

    public function getAllSales()
    {
        $type = 3;
        if ($this->getAuthenticatedUserInformation()->role_id == Role::where('name', 'company')->first(['id'])->id) {
            $type = 1;
        } elseif ($this->getAuthenticatedUserInformation()->role_id == Role::where('name', 'storehouse')->first(['id'])->id) {
            $type = 2;
        }
        $sales = null;
        $from_role = Role::where('name', 'pharmacy')->first(['id'])->id;
        $to_id = Role::where('name', 'pharmacy_sub_user')->first(['id'])->id;
        $pharmacy_role = Role::where('name', 'pharmacy')->first(['id'])->id;
        $pharmacy_sub_user_role = Role::where('name', 'pharmacy_sub_user')->first(['id'])->id;
        $customer_role_id = Role::where('name', 'customer')->first(['id'])->id;

        // Then it's Company To Storehouse
        $sales = Sale::join('sale_details', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', 'products.id')
            ->join('users', function ($join) use ($pharmacy_role, $pharmacy_sub_user_role, $type) {
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
            ->where(function ($query) use ($pharmacy_sub_user_role) {
                // From Company To Storehouse
                // Check if the authenticated user is a pharmacy
                $authenticated_user_role_id = $this->getAuthenticatedUserInformation()->role_id;
                if ($authenticated_user_role_id == $pharmacy_sub_user_role) {
                    $parent_pharmacy_id = SubUser::where('sub_user_id', $this->getAuthenticatedUserId())->first(['parent_id'])->parent_id;
                    if ($parent_pharmacy_id) {
                        // Then Sub User Employeed by some pharmacy , so take the id of that pharmacy
                        $query->where('sales.from_id', '=', $this->getAuthenticatedUserId())
                            ->orWhere('sales.from_id', '=', $parent_pharmacy_id);
                    } else {
                        $query->whereNull('users.id'); // Impossible to Find Match
                    }
                } else {
                    // ! Check it later
                    $query->where('sales.from_id', '=', $this->getAuthenticatedUserId());
                }
            })
            ->select([
                    'sales.id as sale_id',
                    'sales.from_id',
                    'sales.to_id',
                    'sale_details.id as id',
                    'sale_details.product_id',
                    'users.role_id as role_id',
                    'users.full_name',
                    'sale_details.expire_date as expire_date',
                    'sale_details.sel_price as selling_price',
                    'sale_details.pur_price as purchase_price',
                    'sale_details.qty as quantity',
                    'sale_details.con as concentrate',
                    'products.com_name as commercial_name',
                    'products.sc_name as scientefic_name',
                    'products.provider as provider',
                    'products.patch_number',
                    'products.bar_code as bar_code',
                    'products.bonus as bonus',
                    'sales.created_at as created_at',
                    'sales.updated_at as updated_at',
            ])
            ->get();

        return $sales;
    }
}

// ->join('users', function ($join) use ($pharmacy_role_id, $pharmacy_sub_user_role_id) {
                //     $join->on('users.id', 'sales.to_id')
                //         ->where('users.role_id', '=', $pharmacy_role_id)
                //         ->orWhere(function ($bind) use ($pharmacy_sub_user_role_id) {
                //             $bind->where('users.role_id', '=', $pharmacy_sub_user_role_id);

                //             $sub_users_ids = [];

                //             foreach (SubUser::where('parent_id', $this->getAuthenticatedUserId())->get(['sub_user_id']) as $sub_user) {
                //                 $sub_users_ids[] = $sub_user->sub_user_id;
                //             }
                //             // print_r($sub_users_ids);
                //             if ($sub_users_ids) {
                //                 $bind->whereIn('users.id', $sub_users_ids);
                //             } else {
                //                 $bind->whereNull('users.id'); // Impossible to occur
                //             }
                //         })
                //     ;
// })
