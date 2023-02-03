<?php

namespace App\Models\Api\Web\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_id',
        'to_id',
        'type',
        'details',
    ];
}
