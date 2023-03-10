<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Content[]|Collection $contents
 *
 * @method Builder filterName(string|null $name)
 */
class Category extends Model
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
     * @return Content[]|Collection|HasMany
     */
    public function contents(): HasMany|Collection
    {
        return $this->hasMany(Content::class);
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
