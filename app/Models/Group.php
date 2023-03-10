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
 * @property Student[]|Collection $students
 * @property Permission[]|Collection $permissions
 *
 * @property-read string $student_ids
 * @property-read string $permission_ids
 *
 * @method Builder filterName(string|null $name)
 */
class Group extends Model
{
    use HasFactory;

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
    public function students(): Collection|BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * Attributes
     */
    /**
     * @return array
     */
    public function getStudentsIdsAttribute(): array
    {
        return $this->students->pluck('id')->toArray();
    }

    /**
     * Scopes
     */
    public function scopeFilterName(Builder $builder, $name)
    {
        return $builder->when($name, function (Builder $builder, $name) {
            return $builder->where('name', 'like', "%{$name}%");
        });
    }


}
