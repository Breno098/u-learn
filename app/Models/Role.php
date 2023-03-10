<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User[]|Collection $users
 * @property Permission[]|Collection $permissions
 *
 * @property-read string $user_ids
 * @property-read string $permission_ids
 *
 * @method Builder filterName(string|null $name)
 * @method Builder areTeachers()
 * @method Builder areAdmins()
 */
class Role extends Model
{
    use HasFactory;

    const TEACHERS = 'Colaboradores';

    const ADMIN = 'Admin';

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relationships
     */
    /**
     * @return User[]|Collection|BelongsToMany
     */
    public function users(): Collection|BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

     /**
     * @return Permission[]|Collection|BelongsToMany
     */
    public function permissions(): Collection|BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Attributes
     */
    /**
     * @return array
     */
    public function getUserIdsAttribute(): array
    {
        return $this->users->pluck('id')->toArray();
    }

    /**
     * @return array
     */
    public function getPermissionIdsAttribute(): array
    {
        return $this->permissions->pluck('id')->toArray();
    }

    /**
     * Scopes
     */
    public function scopeFilterName(Builder $builder, $name)
    {
        return $builder->when(
            $name,
            function (Builder $builder, $name) {
                return $builder->where('name', 'like', "%{$name}%");
            }
        );
    }

    public function scopeAreTeachers(Builder $query)
    {
        return $query->where('name', self::TEACHERS);
    }

    public function scopeAreAdmins(Builder $query)
    {
        return $query->where('name', self::ADMIN);
    }
}
