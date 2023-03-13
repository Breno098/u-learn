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
 * @property string $description
 * @property Course $course
 * @property Lesson[] $lessons
 */
class Module extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'number',
        'description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

     /**
     * Relationships
     */

    /**
     * @return Course|BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return Lesson[]|HasMany|Collection
     */
    public function lessons(): HasMany|Collection
    {
        return $this->hasMany(Lesson::class)->orderBy('number');
    }
}
