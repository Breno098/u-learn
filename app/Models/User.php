<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int|null $id
 * @property string $name
 * @property string $email
 * @property bool $active
 * @property Carbon $inactivated_at
 * @property string $cpf
 * @property string $phone
 * @property string $profile_image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Role[]|Collection $roles
 *
 * @property-read array $role_ids
 *
 * @method Builder filterLikeName(string|null $name)
 * @method Builder filterLikeEmail(string|null $email)
 * @method Builder filterCpf(string|null $cpf)
 *
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'inactivated_at',
        'cpf',
        'phone',
        'profile_image',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'inactivated_at' => 'datetime',
    ];

    /**
     * Attributes
     */
    /**
     * @return array
     */
    public function getRoleIdsAttribute(): array
    {
        return $this->roles->pluck('id')->toArray();
    }

    /**
     * Set the user's active and inactivated_at.
     *
     * @param string  $value
     * @return void
     */
    public function setActiveAttribute($value)
    {
        $this->attributes['active'] = $value;

        if ($this->inactivated_at === null && $value === false) {
            $this->inactivated_at = now();
        } else if($value) {
            $this->inactivated_at = null;
        }
    }

    /**
     * Relationships
     */
     /**
     * @return Role[]|BelongsToMany|Collection
     */
    public function roles(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return Meeting[]|HasMany|Collection
     */
    public function meetingsTeacher(): HasMany|Collection
    {
        return $this->hasMany(Meeting::class, 'teacher_id');
    }

    /**
     * @return LiveEvent[]|HasMany|Collection
     */
    public function liveEventsResponsible(): HasMany|Collection
    {
        return $this->hasMany(LiveEvent::class, 'responsible_id');
    }

     /**
     * Scopes
     */
    public function scopeFilterLikeName(Builder $builder, $name)
    {
        return $builder->when($name, function (Builder $builder, $name) {
            return $builder->where('name', 'like', "%{$name}%");
        });
    }

    public function scopeFilterLikeEmail(Builder $builder, $email)
    {
        return $builder->when($email, function (Builder $builder, $email) {
            return $builder->where('email', 'like', "%{$email}%");
        });
    }

    public function scopeFilterCpf(Builder $builder, $cpf)
    {
        return $builder->when($cpf, function (Builder $builder, $cpf) {
            return $builder->where('cpf', $cpf);
        });
    }

    public function scopeAreTeachers(Builder $builder)
    {
        return $builder->whereHas('roles', function(Builder $builder){
            $builder->areTeachers();
        });
    }

    public function scopeAreAdmins(Builder $builder)
    {
        return $builder->whereHas('roles', function(Builder $builder){
            $builder->areAdmins();
        });
    }

    public function scopeCreatedThisMonth(Builder $builder)
    {
        return $builder->whereMonth('created_at', now());
    }

    public function scopeCreatedLastMonth(Builder $builder)
    {
        return $builder->whereMonth('created_at', now()->subMonth());
    }

    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('active', true);
    }

    public function scopeInactivatedThisMonth(Builder $builder)
    {
        return $builder->whereMonth('inactivated_at', now());
    }

    public function scopeInactivatedLastMonth(Builder $builder)
    {
        return $builder->whereMonth('inactivated_at', now()->subMonth());
    }
}
