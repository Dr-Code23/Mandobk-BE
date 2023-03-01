<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\Sale
 *
 * @property int $id
 * @property int $from_id
 * @property int $to_id
 * @property string $details
 * @property float $total
 * @property string $type 1 => company_to_storehouse , 2=> storehouse_to_pharmacy , 3=> Pharmacy_to_visitor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUpdatedAt($value)
 * @mixin \Eloquent
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
            get: fn ($val) => json_decode($val),
            set: fn ($val) => json_encode($val)
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'd M Y')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d H:i')
        );
    }
}
