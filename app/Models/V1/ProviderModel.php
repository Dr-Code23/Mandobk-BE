<?php

namespace App\Models\V1;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderModel extends Model
{
    use HasFactory;
    use dateTrait;
    public $table = 'providers';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'name',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }
}
