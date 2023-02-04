<?php

namespace App\Models\Api\V1;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    use dateTrait;
    protected $fillable = [
        'from_id',
        'to_id',
        'type',
        'details',
    ];

    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => json_decode($val)
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
