<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use App\Traits\RoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use DateTrait;
    use RoleTrait;

    public $timestamps = false;
    protected $fillable = [
        'com_name',
        'sc_name',
        'user_id',
        'pur_price',
        'sel_price',
        'bonus',
        'con',
        'limited',
        'barcode',
        'original_total',
        'role_id',
        'created_at'
    ];


    public function product_details()
    {
        return $this->hasMany(ProductInfo::class);
    }
}
