<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\Markting
 *
 * @property int $id
 * @property string $medicine_name
 * @property string $company_name
 * @property float $discount
 * @property string $img
 * @method static \Illuminate\Database\Eloquent\Builder|Markting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Markting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Markting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Markting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Markting whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Markting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Markting whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Markting whereMedicineName($value)
 * @mixin \Eloquent
 */
class Markting extends Model
{
    use HasFactory;
    protected $table = 'markting';
    public $timestamps = false;
    protected $fillable = [
        'medicine_name',
        'company_name',
        'discount',
        'img',
    ];
}
