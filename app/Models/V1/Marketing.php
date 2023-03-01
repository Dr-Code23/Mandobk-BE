<?php

namespace App\Models\V1;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\Marketing
 *
 * @property int $id
 * @property string $medicine_name
 * @property string $company_name
 * @property float $discount
 * @property string $img
 * @method static Builder|Marketing newModelQuery()
 * @method static Builder|Marketing newQuery()
 * @method static Builder|Marketing query()
 * @method static Builder|Marketing whereCompanyName($value)
 * @method static Builder|Marketing whereDiscount($value)
 * @method static Builder|Marketing whereId($value)
 * @method static Builder|Marketing whereImg($value)
 * @method static Builder|Marketing whereMedicineName($value)
 * @mixin Eloquent
 */
class Marketing extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'marketing';
    protected $fillable = [
        'medicine_name',
        'company_name',
        'discount',
        'img',
    ];
}
