<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\HumanResource
 *
 * @property int $id
 * @property int $user_id
 * @property string $status 0 => Attended , 1 => Absense , 2=> Holiday
 * @property string $date
 * @property string|null $attendance
 * @property string|null $departure
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource query()
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereDeparture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HumanResource whereUserId($value)
 * @mixin \Eloquent
 */
class HumanResource extends Model
{
    use HasFactory;
    use DateTrait;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'status',
        'date',
        'attendance',
        'departure',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departure(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'H:i'),
        );
    }

    public function attendance(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'H:i'),
        );
    }
}
