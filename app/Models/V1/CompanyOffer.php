<?php

namespace App\Models\V1;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\CompanyOffer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyOffer query()
 * @mixin \Eloquent
 */
class CompanyOffer extends Model
{
    use HasFactory;
    use DateTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        // 'sc_name',
        // 'com_name',
        'bonus',
        // 'expire_date',
        'product_id',
        'pay_method',
        'offer_duration',
        'created_at',
        'updated_at',
        'user_id',
    ];

    public function expireDate(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
            set: fn ($val) => $this->changeDateFormat($val, 'Y-m-d')
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($val) => $this->changeDateFormat($val, 'Y-m-d'),
        );
    }
}
