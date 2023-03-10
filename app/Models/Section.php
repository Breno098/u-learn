<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property bool $can_delete
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Content[]|Collection $contents
 *
 * @method Builder filterName(string|null $name)
 * @method Builder canDelete()
 */
class Section extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'can_delete'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_delete' => 'boolean',
    ];

    /**
     * Relationships
     */
     /**
     * @return Content[]|Collection|BelongsToMany
     */
    public function contents(): Collection|BelongsToMany
    {
        return $this->belongsToMany(Content::class);
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

    public function scopeCanDelete(Builder $builder)
    {
        return $builder->where('can_delete', true);
    }
}
