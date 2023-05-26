<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 * @property-read User $user
 *
 * @method static Builder|Offer newModelQuery()
 * @method static Builder|Offer newQuery()
 * @method static Builder|Offer query()
 * @method static Builder|Offer whereCreatedAt($value)
 * @method static Builder|Offer whereFrom($value)
 * @method static Builder|Offer whereId($value)
 * @method static Builder|Offer wherePayMethod($value)
 * @method static Builder|Offer whereProductId($value)
 * @method static Builder|Offer whereStatus($value)
 * @method static Builder|Offer whereTo($value)
 * @method static Builder|Offer whereType($value)
 * @method static Builder|Offer whereUpdatedAt($value)
 * @method static Builder|Offer whereUserId($value)
 *
 * @mixin Eloquent
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
