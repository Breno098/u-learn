<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property Group[]|Collection $groups
 * @property Address $address
 *
 * @property-read array $group_ids
 *
 * @method Builder areStudents()
 * @method Builder filterLikeName(string|null $name)
 * @method Builder filterLikeEmail(string|null $email)
 * @method Builder filterCpf(string|null $cpf)
 *
 */
class Student extends Model
{
    use HasFactory;

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
        'customer_cpf',
        'customer_phone',
        'customer_address',
        'equal_data',
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
        'equal_data' => 'boolean',
    ];

    /**
     * Attributes
     */
    /**
     * @return array
     */
    public function getGroupIdsAttribute(): array
    {
        return $this->groups->pluck('id')->toArray();
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
     * @return Group[]|BelongsToMany|Collection
     */
    public function groups(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Group::class);
    }


     /**
     * Scopes
     */
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
