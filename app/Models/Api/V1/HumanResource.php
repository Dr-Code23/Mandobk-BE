<?php

namespace App\Models\Api\V1;

use App\Models\User;
use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanResource extends Model
{
    use HasFactory;
    use dateTrait;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'status',
        'date',
        'attendance',
        'departure',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departure(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'H:i'),
            set: fn ($val) => $this->changeDateFormat($val, 'H:i')
        );
    }

    public function attendance(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'H:i'),
            set: fn ($val) => $this->changeDateFormat($val, 'H:i')
        );
    }
}
