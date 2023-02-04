<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataEntry extends Model
{
    use HasFactory;
    protected $table = 'data_entry';
    protected $fillable = [
        'com_name',
        'sc_name',
        'qty',
        'pur_price',
        'sel_price',
        'bonus',
        'con',
        'patch_number',
        'limited',
        'provider',
        'bar_code',
        'entry_date',
        'expire_date',
    ];
    protected $casts = [
        'entry_date' => 'datetime',
        'expire_date' => 'datetime',
    ];
}
