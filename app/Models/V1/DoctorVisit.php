<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\DoctorVisit
 *
 * @property int $id
 * @property int $visitor_recipe_id
 * @property int $doctor_id
 * @property string $created_at
 * @method static Builder|DoctorVisit newModelQuery()
 * @method static Builder|DoctorVisit newQuery()
 * @method static Builder|DoctorVisit query()
 * @method static Builder|DoctorVisit whereCreatedAt($value)
 * @method static Builder|DoctorVisit whereDoctorId($value)
 * @method static Builder|DoctorVisit whereId($value)
 * @method static Builder|DoctorVisit whereVisitorRecipeId($value)
 * @mixin Eloquent
 */
class DoctorVisit extends Model
{
    use HasFactory;
    use DateTrait;

    public $timestamps = false;
    protected $fillable = [
        'visitor_recipe_id',
        'doctor_id',
        'created_at',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
