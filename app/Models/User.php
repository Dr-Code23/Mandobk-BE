<?php

namespace App\Models;

use App\Models\V1\HumanResource;
use App\Models\V1\Role;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @method create(array $array)
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use DateTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'phone',
        'role_id',
        'status',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Fetch Role Through One To One Relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    /**
     * Return An Accessor For Created At.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getCreatedAtAttribute($value)
    {
        return $this->changeDateFormat($value);
    }

    /**
     * Return An Accessor For Updated At.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getUpdatedAtAttribute($value)
    {
        return $this->changeDateFormat($value);
    }

    public function HumanResources()
    {
        return $this->hasMany(HumanResource::class, 'user_id', 'id');
    }

    public function password(): CastsAttribute
    {
        return CastsAttribute::make(
            set: fn ($val) => Hash::check($val, $this->password) ? $val : Hash::make($val)
        );
    }
}
