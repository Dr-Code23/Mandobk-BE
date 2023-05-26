<?php

namespace App\Models\V1;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\V1\PayMethod
 *
 * @property int $id
 * @property string $name
 *
 * @method static Builder|PayMethod newModelQuery()
 * @method static Builder|PayMethod newQuery()
 * @method static Builder|PayMethod query()
 * @method static Builder|PayMethod whereId($value)
 * @method static Builder|PayMethod whereName($value)
 *
 * @mixin Eloquent
 */
class PayMethod extends Model
{
    use HasFactory;

    public $timestamps = false;
}
