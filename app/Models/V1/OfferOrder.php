<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\OfferOrder
 *
 * @property int $id
 * @property int $offer_id
 * @property int $want_offer_id
 * @property int $qty
 * @property string $status 0 => rejected , 1=> pending , 2=> approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferOrder whereWantOfferId($value)
 * @mixin \Eloquent
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

    public function comments()
    {
        return $this->hasMany(User::class);
    }
}
