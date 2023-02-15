<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            get: fn ($val) => $val->changeDateFormat($val, 'Y-m-d H:i'),
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
