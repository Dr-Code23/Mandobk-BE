<?php

namespace App\Models;

use App\Models\V1\HumanResource;
use App\Models\V1\Role;
use App\Traits\DateTrait;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HigherOrderCollectionProxy;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @method create(array $array)
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $role_id
 * @property string|null $avatar
 * @property string $status 0 => Deleted , 1=> Active , 2=> Frozen
 * @property string $full_name
 * @property string|null $phone
 * @property string|null $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, HumanResource> $HumanResources
 * @property-read int|null $human_resources_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Role|null $role
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereFullName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRoleId($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 *
 * @property-read Collection<int, HumanResource> $HumanResources
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read Collection<int, HumanResource> $HumanResources
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read Collection<int, HumanResource> $HumanResources
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read Collection<int, HumanResource> $HumanResources
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read Collection<int, PersonalAccessToken> $tokens
 *
 * @mixin Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use DateTrait;

    /**
     * @var HigherOrderCollectionProxy|mixed
     */
    public mixed $unreadNotifications;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'phone',
        'role_id',
        'status',
        'password',
        'avatar',
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
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Fetch Role Through One-To-One Relationship.
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function HumanResources(): HasMany
    {
        return $this->hasMany(HumanResource::class, 'user_id', 'id');
    }

    public function password(): CastsAttribute
    {
        return CastsAttribute::make(
            set: fn ($val) => Hash::check($val, $this->password) ? $val : Hash::make($val)
        );
    }

    /**
     * Return An Accessor For Created At.
     *
     * @param  mixed  $value
     */
    protected function getCreatedAtAttribute($value): string
    {
        return $this->changeDateFormat($value);
    }

    /**
     * Return An Accessor For Updated At.
     *
     * @param  mixed  $value
     */
    protected function getUpdatedAtAttribute($value): string
    {
        return $this->changeDateFormat($value);
    }
}
