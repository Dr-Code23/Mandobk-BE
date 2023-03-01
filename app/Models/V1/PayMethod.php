<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\PayMethod
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod whereName($value)
 * @mixin \Eloquent
 */
class PayMethod extends Model
{
    use HasFactory;
    public $timestamps = false;
}
