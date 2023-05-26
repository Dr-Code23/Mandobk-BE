<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
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
 *
 * @method static Builder|PharmacyVisit newModelQuery()
 * @method static Builder|PharmacyVisit newQuery()
 * @method static Builder|PharmacyVisit query()
 * @method static Builder|PharmacyVisit whereCreatedAt($value)
 * @method static Builder|PharmacyVisit whereDoctorId($value)
 * @method static Builder|PharmacyVisit whereId($value)
 * @method static Builder|PharmacyVisit wherePharmacyId($value)
 * @method static Builder|PharmacyVisit whereVisitorRecipeId($value)
 *
 * @mixin Eloquent
 */
class PharmacyVisit extends Model
{
    use HasFactory, DateTrait;

    public $timestamps = false;

    protected $fillable = [
        'visitor_recipe_id',
        'doctor_id',
        'pharmacy_id',
        'created_at',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
