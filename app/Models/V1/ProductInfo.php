<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\GeneralTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\V1\ProductInfo
 *
 * @property int $id
 * @property int $role_id
 * @property int $product_id
 * @property int $qty
 * @property string $patch_number
 * @property string $expire_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ProductInfo newModelQuery()
 * @method static Builder|ProductInfo newQuery()
 * @method static Builder|ProductInfo query()
 * @method static Builder|ProductInfo whereCreatedAt($value)
 * @method static Builder|ProductInfo whereExpireDate($value)
 * @method static Builder|ProductInfo whereId($value)
 * @method static Builder|ProductInfo wherePatchNumber($value)
 * @method static Builder|ProductInfo whereProductId($value)
 * @method static Builder|ProductInfo whereQty($value)
 * @method static Builder|ProductInfo whereRoleId($value)
 * @method static Builder|ProductInfo whereUpdatedAt($value)
 *
 * @mixin Eloquent
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
        'expire_date',
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
