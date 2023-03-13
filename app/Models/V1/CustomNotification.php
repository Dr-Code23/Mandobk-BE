<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\CustomNotification
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property int|null $role_id
 * @property string $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomNotification extends Model
{
    use HasFactory;
    use DateTrait;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $casts = [
        //'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'read_at',
    ];

//    public function readAt():Attribute{
//        return Attribute::make(
//            get: fn($val) => $this->changeDateFormat($val , 'Y-m-d H:i')
//        );
//    }

}
