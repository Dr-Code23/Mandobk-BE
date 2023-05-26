<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\SubUser
 *
 * @property int $id
 * @property int $parent_id
 * @property int $sub_user_id
 *
 * @method static Builder|SubUser newModelQuery()
 * @method static Builder|SubUser newQuery()
 * @method static Builder|SubUser query()
 * @method static Builder|SubUser whereId($value)
 * @method static Builder|SubUser whereParentId($value)
 * @method static Builder|SubUser whereSubUserId($value)
 *
 * @mixin Eloquent
 */
class SubUser extends Model
{
    use HasFactory;
    use DateTrait;

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'sub_user_id',
    ];

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }
}
