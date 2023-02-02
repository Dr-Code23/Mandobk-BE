<?php

namespace App\Models\Api\Web\V1;

use App\Traits\dateTrait;
use App\Traits\roleTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    use dateTrait;
    use roleTrait;
    protected $fillable = [
        'com_name',
        'sc_name',
        'role_id',
        'user_id',
        'qty',
        'pur_price',
        'sel_price',
        'bonus',
        'con',
        'patch_number',
        'limited',
        'provider',
        'bar_code',
        'entry_date',
        'expire_date',
        'created_at',
        'updated_at',
    ];

    public function patchNumber(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                // Return The Original Path Number For All Users Except Admins
                if (!in_array($this->role_id, [Role::where('name', 'ceo')->first(['id'])->id, Role::where('name', 'data_entry')->first(['id'])->id])) {
                    return $val;
                }
                // Check If role_name cached instead of fetch it again from DB
                $role_name = Cache::get($this->role_id);
                if (!$role_name) {
                    $role_name = Role::where('id', $this->role_id)->first(['name'])->name;
                    Cache::set($this->role_id, $role_name);
                }

                return config('roles.role_patch_number_symbol.'.$role_name).'-'.$val;
            }
        );
    }

    public function entryDate(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
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
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
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
