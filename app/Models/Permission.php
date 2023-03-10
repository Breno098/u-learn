<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $label
 * @property string $key
 * @property string|null $level
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'key',
        'level',
        'type'
    ];

    /**
     * Relationships
     */
    /**
     * @return Group[]|Collection|BelongsToMany
     */
    public function groups(): Collection|BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }
}
