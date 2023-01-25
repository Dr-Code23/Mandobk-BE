<?php

namespace App\Models\Api\Web\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    public $timestamps = false;
    protected $fillable = [
        'com_name',
        'sc_name',
        'qty',
        'pur_price',
        'sel_price',
        'bonus',
        'con',
        'patch_number',
        'provider',
        'qr_code',
        'created_at',
        'expire_in',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expire_in' => 'datetime',
    ];
}
