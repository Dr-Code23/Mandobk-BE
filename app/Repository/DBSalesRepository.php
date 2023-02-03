<?php

namespace App\Repository;

use App\Models\Api\Web\V1\Role;
use App\Models\Api\Web\V1\Sale;
use App\Models\Api\Web\V1\SubUser;
use App\RepositoryInterface\SalesRepositoryInterface;
use App\Traits\userTrait;

class DBSalesRepository implements SalesRepositoryInterface
{
    use userTrait;

    /**
     * @return mixed
     */
    public function getAllSales(int $type)
    {
        $sales = null;
        $pharmacy_role = Role::where('name', 'pharmacy')->first(['id'])->id;
        $pharmacy_sub_user_role = Role::where('name', 'pharmacy_sub_user')->first(['id'])->id;

        // Then it's Company To Storehouse
        $sales = Sale::join('users', function ($join, $pharmacy_role, $pharmacy_sub_user_role, $type) {
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
                /*
                This Condition check if the logged user is pharmacy Admin user
                    if $parent_id is null , then the logged user
                */
                $sub_users = [];
                $parent_id = SubUser::where('sub_user_id', $this->getAuthenticatedUserId())->first(['parent_id']);
                // If $parent_id is null , then the logged user is pharmacy Admin User , so get all it sub users
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
        ->get();
        // $sales = Sale::join('sale_details', 'sales.id', 'sale_details.sale_id')
        //     ->join('products', 'sale_details.product_id', 'products.id')
        //     ->join('users', function ($join) use ($pharmacy_role, $pharmacy_sub_user_role, $type) {
        //         $join->on('users.id', 'sales.to_id')
        //             ->where(function ($query) use ($pharmacy_role, $pharmacy_sub_user_role, $type) {
        //                 // From Company To Storehouse
        //                 if ($type == 1) {
        //                     $query->where('users.role_id', Role::where('name', 'storehouse')->first(['id'])->id);
        //                 }
        //                 // From Storehouse To Pharmacy (Admin or sub_user)
        //                 elseif ($type == 2) {
        //                     $query->where('users.role_id', '=', $pharmacy_role)
        //                         ->orWhere('users.role_id', '=', $pharmacy_sub_user_role);
        //                 }
        //                 // From Pharmacy(Admin or sub_user) to random customer
        //                 elseif ($type == 3) {
        //                     $query->where('users.role_id', '=', Role::where('name', 'customer')->first(['id'])->id);
        //                 } else {
        //                     $query->where('users.role_id', '=', 1000); // Impossible to occur
        //                 }
        //             }
        //             );
        //     })
        //     ->where(function ($query) use ($pharmacy_sub_user_role) {
        //         // From Company To Storehouse
        //         // Check if the authenticated user is a pharmacy
        //         $authenticated_user_role_id = $this->getAuthenticatedUserInformation()->role_id;
        //         if ($authenticated_user_role_id == $pharmacy_sub_user_role) {
        //             $parent_pharmacy_id = SubUser::where('sub_user_id', $this->getAuthenticatedUserId())->first(['parent_id'])->parent_id;
        //             if ($parent_pharmacy_id) {
        //                 // Then Sub User Employeed by some pharmacy , so take the id of that pharmacy
        //                 $query->where('sales.from_id', '=', $this->getAuthenticatedUserId())
        //                     ->orWhere('sales.from_id', '=', $parent_pharmacy_id);
        //             } else {
        //                 $query->whereNull('users.id'); // Impossible to Find Match
        //             }
        //         } else {
        //             // ! Check it later
        //             $query->where('sales.from_id', '=', $this->getAuthenticatedUserId());
        //         }
        //     })
        //     ->select([
        //             'sales.id as sale_id',
        //             'sales.from_id',
        //             'sales.to_id',
        //             'sale_details.id as id',
        //             'sale_details.product_id',
        //             'users.role_id as role_id',
        //             'users.full_name',
        //             'sale_details.expire_date as expire_date',
        //             'sale_details.sel_price as selling_price',
        //             'sale_details.pur_price as purchase_price',
        //             'sale_details.qty as quantity',
        //             'sale_details.con as concentrate',
        //             'products.com_name as commercial_name',
        //             'products.sc_name as scientefic_name',
        //             'products.provider as provider',
        //             'products.patch_number',
        //             'products.bar_code as bar_code',
        //             'products.bonus as bonus',
        //             'sales.created_at as created_at',
        //             'sales.updated_at as updated_at',
        //     ])
        //     ->get();
    }

    /**
     * @param mixed $request
     *
     * @return mixed
     */
    public function storeSale($request)
    {
    }
}
