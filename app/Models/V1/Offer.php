<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
