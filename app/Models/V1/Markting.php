<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Markting extends Model
{
    use HasFactory;
    protected $table = 'markting';
    public $timestamps = false;
    protected $fillable = [
        'medicine_name',
        'company_name',
        'discount',
        'img',
    ];
}
