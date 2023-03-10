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
 * @property string $author
 * @property string $responsible_for_production
 * @property string $production_type
 * @property string $embed
 * @property boolean $has_seasons
 * @property boolean $highlight
 * @property int $category_id
 * @property int $top_position
 * @property int $access_count
 * @property int $number_of_seasons
 * @property string $main_image
 * @property string $secondary_image
 * @property string $descritiption_image
 * @property string $screensaver_image
 * @property Carbon $launch_start_at
 * @property Carbon $launch_end_at
 * @property Carbon $end_at
 * @property Section[]|Collection $sections
 * @property Season[]|Collection $seasons
 * @property Extra[]|Collection $extras
 * @property ContentTag[]|Collection $tags
 * @property Chapter $chapter
 * @property Category $category
 * @property Genre $genre
 *
 * @property-read array $sections_ids
 */
class Content extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'launch_start_at',
        'launch_end_at',
        'author',
        'responsible_for_production',
        'production_type',
        'end_at',
        'has_seasons',
        'number_of_seasons',
        'highlight',
        'top_position',
        'access_count',
        'main_image',
        'secondary_image',
        'descritiption_image',
        'screensaver_image',
        'category_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'launch_start_at' => 'datetime',
        'launch_end_at' => 'datetime',
        'end_at' => 'datetime',
        'has_seasons' => 'boolean',
        'highlight' => 'boolean',
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
     * @return Section[]|Collection|BelongsToMany
     */
    public function sections(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Section::class);
    }

    /**
     * @return Section[]|Collection|BelongsToMany
     */
    public function sectionsOrderedByName(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Section::class)->orderBy('name');
    }

     /**
     * @return Extra[]|Collection|HasMany
     */
    public function extras(): HasMany|Collection
    {
        return $this->hasMany(Extra::class);
    }

     /**
     * @return Content[]|Collection|BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_tag', 'content_id', 'content_tag_id')
            ->using(ContentTag::class)
            ->withPivot('type');
    }

    /**
     * @return Content[]|Collection|BelongsToMany
     */
    public function contentsOfTheSameCollection(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_tag', 'content_id', 'content_tag_id')
            ->using(ContentTag::class)
            ->withPivot('type')
            ->withPivotValue('type', 'mesma colecao');
    }

    /**
     * @return Content[]|Collection|BelongsToMany
     */
    public function similarContents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_tag', 'content_id', 'content_tag_id')
            ->using(ContentTag::class)
            ->withPivot('type')
            ->withPivotValue('type', 'semelhantes');
    }

    /**
     * Attributes
     */
    /**
     * @return array
     */
    public function getSectionIdsAttribute(): array
    {
        return $this->sections->pluck('id')->toArray();
    }

    /**
     * @return array
     */
    public function getGenresIdsAttribute(): array
    {
        return $this->genres->pluck('id')->toArray();
    }

    /**
     * Actions
     */
     /**
      * @param integer $topPosition
      * @return boolean
      */
    public function setTopPosition(int $topPosition): bool
    {
        return $this->update([
            'top_position' => $topPosition
        ]);
    }

    /**
     * @return boolean
     */
    public function removeTopPosition(): bool
    {
        return $this->update([
            'top_position' => null
        ]);
    }
}
