<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyVisit extends Model
{
    use HasFactory, DateTrait;
    protected $fillable = [
        'visitor_recipe_id',
        'doctor_id',
        'pharmacy_id',
        'created_at'
    ];
    public $timestamps = false;
    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }
    public function pharmacy_user()
    {
        return $this->belongsTo(User::class, 'pharmacy_id', 'id');
    }
}
