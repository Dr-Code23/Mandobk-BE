<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\V1\Sale
 *
 * @property int $id
 * @property int $from_id
 * @property int $to_id
 * @property string $details
 * @property float $total
 * @property string $type 1 => company_to_storehouse , 2=> storehouse_to_pharmacy , 3=> Pharmacy_to_visitor
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Sale newModelQuery()
 * @method static Builder|Sale newQuery()
 * @method static Builder|Sale query()
 * @method static Builder|Sale whereCreatedAt($value)
 * @method static Builder|Sale whereDetails($value)
 * @method static Builder|Sale whereFromId($value)
 * @method static Builder|Sale whereId($value)
 * @method static Builder|Sale whereToId($value)
 * @method static Builder|Sale whereTotal($value)
 * @method static Builder|Sale whereType($value)
 * @method static Builder|Sale whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Sale extends Model
{
    use HasFactory;
    use DateTrait;

    protected $fillable = [
        'from_id',
        'to_id',
        'type',
        'details',
        'total',
    ];

    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn($val) => json_decode($val),
            set: fn($val) => json_encode($val)
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($val) => $this->changeDateFormat($val, 'd M Y')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
