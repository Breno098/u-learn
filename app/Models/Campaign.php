<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method Builder filterLikeName(string|null $name)
 */
class Campaign extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * @return Item[]|Collection|HasMany
     */
    public function items(): Collection|HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @return Order[]|Collection|HasMany
     */
    public function orders(): Collection|HasMany
    {
        return $this->hasMany(Order::class);
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
}
