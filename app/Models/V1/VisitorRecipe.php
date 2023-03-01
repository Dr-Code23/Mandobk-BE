<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\VisitorRecipe
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $random_number
 * @property string $alias
 * @property string $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $visitor
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe query()
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereRandomNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VisitorRecipe whereVisitorId($value)
 * @mixin \Eloquent
 */
class VisitorRecipe extends Model
{
    use HasFactory;
    use DateTrait;
    protected $fillable = [
        'visitor_id',
        'random_number',
        'details',
        'alias',
    ];

    protected function details(): Attribute
    {
        return Attribute::make(
            set: fn ($val) => json_encode($val),
            get: fn ($val) => json_decode($val, true)
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),

        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id', 'id');
    }
}
