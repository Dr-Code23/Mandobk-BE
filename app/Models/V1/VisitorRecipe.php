<?php

namespace App\Models\V1;

use App\Models\User;
use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\V1\VisitorRecipe
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $random_number
 * @property string $alias
 * @property string $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $visitor
 * @method static Builder|VisitorRecipe newModelQuery()
 * @method static Builder|VisitorRecipe newQuery()
 * @method static Builder|VisitorRecipe query()
 * @method static Builder|VisitorRecipe whereAlias($value)
 * @method static Builder|VisitorRecipe whereCreatedAt($value)
 * @method static Builder|VisitorRecipe whereDetails($value)
 * @method static Builder|VisitorRecipe whereId($value)
 * @method static Builder|VisitorRecipe whereRandomNumber($value)
 * @method static Builder|VisitorRecipe whereUpdatedAt($value)
 * @method static Builder|VisitorRecipe whereVisitorId($value)
 * @mixin Eloquent
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

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),

        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($val) => $this->changeDateFormat($val, 'Y-m-d H:i'),
        );
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_id', 'id');
    }

    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn($val) => json_decode($val, true),
            set: fn($val) => json_encode($val)
        );
    }
}
