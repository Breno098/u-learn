<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int|null $id
 * @property string $name
 * @property string $number
 * @property string $image
 * @property string $description
 * @property Course $course
 * @property Lesson[]|Collection|MorphToMany $lessons
 * @property Exam[]|Collection|MorphToMany $exams
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
     * @return Lesson[]|MorphToMany|Collection
     */
    public function lessons(): MorphToMany|Collection
    {
        return $this->morphedByMany(Lesson::class, 'itemable')->withPivot('number')->orderByPivot('number');
    }

    /**
     * @return Exam[]|MorphToMany|Collection
     */
    public function exams(): MorphToMany|Collection
    {
        return $this->morphedByMany(Exam::class, 'itemable')->withPivot('number')->orderByPivot('number');
    }

    /**
     * @return Lesson[]|Exam[]|MorphToMany|Collection
     */
    public function allItems()
    {
        return collect($this->lessons)->concat($this->exams)->sortBy('pivot_number');
    }
}
