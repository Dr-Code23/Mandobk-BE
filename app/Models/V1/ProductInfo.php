<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\GeneralTrait;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProductInfo extends Model
{
    use HasFactory;
    use DateTrait;
    use GeneralTrait;
    protected $table = 'products_info';

    protected $fillable = [
        'role_id',
        'product_id',
        'qty',
        'patch_number',
        'expire_date'
    ];


    public function patchNumber(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                return $this->formatPatchNumber($val, $this->role_id);
            }
        );
    }


    public function expireDate(): Attribute
    {
        return Attribute::make(
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
        );
    }
}
