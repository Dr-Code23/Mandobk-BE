<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProductInfo extends Model
{
    use HasFactory;
    use DateTrait;
    protected $table = 'products_info';

    protected $fillable = [
        'product_id',
        'qty',
        'patch_number',
        'expire_date'
    ];


    public function patchNumber(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                // Cache All Roles Ids
                if (!Cache::get('all_roles')) {
                    $roles = [];
                    foreach (Role::all(['id', 'name']) as $role) {
                        $roles[$role->name] = $role->id;
                    }
                    Cache::set('all_roles', $roles);
                }
                $all_roles = Cache::get('all_roles');

                // Return The Original Path Number For All Users Except Admins
                $authenticated_user_role_id = $this->getAuthenticatedUserInformation()->role_id;
                if (!in_array($authenticated_user_role_id, [
                    $all_roles['ceo'],
                    $all_roles['data_entry'],
                ])) {
                    return $val;
                }
                foreach ($all_roles as $role_name => $role_id) {
                    if ($role_id == $this->role_id) {
                        return config('roles.role_patch_number_symbol.' . $role_name) . '-' . $val;
                    }
                }
            }
        );
    }


    public function expireDate(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }
}
