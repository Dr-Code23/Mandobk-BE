<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderModel extends Model
{
    use HasFactory;
    public $table = 'providers';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'name'
    ];
}
