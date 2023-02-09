<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUser extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'parent_id',
        'sub_user_id',
    ];
}