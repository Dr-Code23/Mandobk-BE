<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method whereIn(string $string, \Closure $param)
 */
class Archive extends Model
{
    use HasFactory, DateTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'details',
        'random_number'
    ];

    /**
     * @return Attribute
     */
    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => json_decode($val, true),
            set: fn ($val) => json_encode($val)
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }

    /**
     * @return Attribute
     */
    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
