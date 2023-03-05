<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
