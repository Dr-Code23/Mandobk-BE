<?php

namespace App\Models\V1;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorRecipe extends Model
{
    use HasFactory;
    use dateTrait;
    protected $fillable = [
        'visitor_id',
        'random_number',
        'details',
        'alias',
    ];

    protected function details(): Attribute
    {
        return Attribute::make(
            set: fn ($val) => json_encode($val),
            get: fn ($val) => json_decode($val)
        );
    }

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
}