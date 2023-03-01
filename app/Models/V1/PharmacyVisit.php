<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\PharmacyVisit
 *
 * @property int $id
 * @property int $visitor_recipe_id
 * @property int $doctor_id
 * @property int $pharmacy_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit wherePharmacyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PharmacyVisit whereVisitorRecipeId($value)
 * @mixin \Eloquent
 */
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
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
