<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\Offer
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property string $from
 * @property string $to
 * @property int $pay_method
 * @property string $type 1=>Company Offer , 2=> Storehouse Offer
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\V1\Product $product
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer wherePayMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUserId($value)
 * @mixin \Eloquent
 */
class Offer extends Model
{
    use HasFactory;
    use DateTrait;
    protected $fillable = [
        'product_id',
        'pay_method',
        'from',
        'to',
        'status',
        'created_at',
        'updated_at',
        'user_id',
        'type',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }
    public function from(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }
    public function to(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }



    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
