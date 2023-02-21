<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model
{
    use HasFactory;
    use DateTrait;
    protected $fillable = [
        'type',
        'read',
        'payload',
    ];


    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d/H:i:s')
        );
    }

    public function getUnread()
    {
        return $this->where('read', '0');
    }
}
