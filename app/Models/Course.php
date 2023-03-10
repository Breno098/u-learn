<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $descritiption
 * @property string $level
 * @property Carbon $duration
 * @property string $video
 * @property string $url_sale
 * @property int $category_id
 * @property int $certificate_id
 * @property string $wallpaper_image
 * @property string $tumb_image
 * @property Season[]|Collection $seasons
 * @property Chapter $chapter
 * @property Category $category
 * @property Genre $genre
 *
 * @property-read array $sections_ids
 */
class Course extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'descritiption',
        'level',
        'duration',
        'video',
        'url_sale',
        'category_id',
        'certificate_id',
        'wallpaper_image',
        'tumb_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'datetime',
    ];

     /**
     * Relationships
     */
    /**
     * @return Category|BelongsTo
     */
    public function category(): Category|BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    /**
     * @return Genre[]|Collection|BelongsToMany
     */
    public function genres(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return Chapter|MorphOne
     */
    public function chapter(): Chapter|MorphOne
    {
        return $this->morphOne(Chapter::class, 'chapterable');
    }

    /**
     * @return Season[]|Collection|HasMany
     */
    public function seasons(): HasMany|Collection
    {
        return $this->hasMany(Season::class)->orderBy('number');
    }

    /**
     * @return Extra[]|Collection|HasMany
     */
    public function extras(): HasMany|Collection
    {
        return $this->hasMany(Extra::class);
    }

    /**
     * Attributes
     */

    /**
     * @return array
     */
    public function getGenresIdsAttribute(): array
    {
        return $this->genres->pluck('id')->toArray();
    }
}
