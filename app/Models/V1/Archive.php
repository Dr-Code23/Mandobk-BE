<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\Archive
 *
 * @method whereIn(string $string, \Closure $param)
 * @property int $id
 * @property int $random_number
 * @property string $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Archive newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Archive newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Archive query()
 * @method static \Illuminate\Database\Eloquent\Builder|Archive whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Archive whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Archive whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Archive whereRandomNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Archive whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Archive extends Model
{
    use HasFactory, DateTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'details',
        'random_number'
    ];

    /**
     * @return Attribute
     */
    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => json_decode($val, true),
            set: fn ($val) => json_encode($val)
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }

    /**
     * @return Attribute
     */
    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }
}
