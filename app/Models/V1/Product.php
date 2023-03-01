<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\RoleTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\V1\Product
 *
 * @property int $id
 * @property int $role_id
 * @property int $user_id
 * @property string $com_name
 * @property string $sc_name
 * @property float $pur_price
 * @property float $sel_price
 * @property float $bonus
 * @property float $con
 * @property string $barcode
 * @property string|null $original_total
 * @property int $limited
 * @property string $created_at
 * @property-read Collection<int, ProductInfo> $product_details
 * @property-read int|null $product_details_count
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBarcode($value)
 * @method static Builder|Product whereBonus($value)
 * @method static Builder|Product whereComName($value)
 * @method static Builder|Product whereCon($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereLimited($value)
 * @method static Builder|Product whereOriginalTotal($value)
 * @method static Builder|Product wherePurPrice($value)
 * @method static Builder|Product whereRoleId($value)
 * @method static Builder|Product whereScName($value)
 * @method static Builder|Product whereSelPrice($value)
 * @method static Builder|Product whereUserId($value)
 * @mixin Eloquent
 */
class Product extends Model
{
    use HasFactory;
    use DateTrait;
    use RoleTrait;

    public $timestamps = false;
    protected $fillable = [
        'com_name',
        'sc_name',
        'user_id',
        'pur_price',
        'sel_price',
        'bonus',
        'con',
        'limited',
        'barcode',
        'original_total',
        'role_id',
        'created_at'
    ];


    public function product_details(): HasMany
    {
        return $this->hasMany(ProductInfo::class);
    }
}
