<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int|null $id
 * @property string $name
 * @property string $number
 * @property string $image
 * @property int $number_of_chapters
 * @property Content $content
 */
class Season extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'number',
        'number_of_chapters',
        'image',
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
     * Relationships
     */

    /**
     * @return Content|BelongsTo
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * @return Chapter[]|Collection|MorphMany
     */
    public function chapters(): MorphMany|Collection
    {
        return $this->morphMany(Chapter::class, 'chapterable')->orderBy('number');
    }
}
