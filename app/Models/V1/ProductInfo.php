<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\GeneralTrait;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\V1\ProductInfo
 *
 * @property int $id
 * @property int $role_id
 * @property int $product_id
 * @property int $qty
 * @property string $patch_number
 * @property string $expire_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo wherePatchNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductInfo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
