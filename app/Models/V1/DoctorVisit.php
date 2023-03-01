<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
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
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorVisit whereVisitorRecipeId($value)
 * @mixin \Eloquent
 */
class DoctorVisit extends Model
{
    use HasFactory;
    use DateTrait;
    protected $fillable = [
        'visitor_recipe_id',
        'doctor_id',
        'created_at',
    ];
    public $timestamps = false;

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
