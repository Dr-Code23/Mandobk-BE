<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\V1\ProductInfo> $product_details
 * @property-read int|null $product_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereComName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLimited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOriginalTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePurPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereScName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSelPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUserId($value)
 * @mixin \Eloquent
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


    public function product_details()
    {
        return $this->hasMany(ProductInfo::class);
    }
}
