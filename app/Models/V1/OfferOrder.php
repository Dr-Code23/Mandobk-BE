<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\V1\OfferOrder
 *
 * @property int $id
 * @property int $offer_id
 * @property int $want_offer_id
 * @property int $qty
 * @property string $status 0 => rejected , 1=> pending , 2=> approved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|OfferOrder newModelQuery()
 * @method static Builder|OfferOrder newQuery()
 * @method static Builder|OfferOrder query()
 * @method static Builder|OfferOrder whereCreatedAt($value)
 * @method static Builder|OfferOrder whereId($value)
 * @method static Builder|OfferOrder whereOfferId($value)
 * @method static Builder|OfferOrder whereQty($value)
 * @method static Builder|OfferOrder whereStatus($value)
 * @method static Builder|OfferOrder whereUpdatedAt($value)
 * @method static Builder|OfferOrder whereWantOfferId($value)
 *
 * @mixin Eloquent
 */
class OfferOrder extends Model
{
    use HasFactory;
    use DateTrait;

    protected $fillable = [
        'offer_id',
        'want_offer_id',
        'qty',
        'status',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
