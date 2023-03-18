<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $number
 * @property string $image
 * @property string $description
 * @property Course $course
 * @property Lesson[]|\Illuminate\Database\Eloquent\Collection|MorphToMany $lessons
 * @property Exam[]|\Illuminate\Database\Eloquent\Collection|MorphToMany $exams
 * @property-read Lesson[]|Exam[]|Collection $all_items
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
     * @return Lesson[]|MorphToMany|\Illuminate\Database\Eloquent\Collection
     */
    public function lessons(): MorphToMany|\Illuminate\Database\Eloquent\Collection
    {
        return $this->morphedByMany(Lesson::class, 'itemable')->withPivot([
            'number as number',
            "itemable_type as type"
        ])->orderByPivot('number');
    }

    /**
     * @return Exam[]|MorphToMany|\Illuminate\Database\Eloquent\Collection
     */
    public function exams(): MorphToMany|\Illuminate\Database\Eloquent\Collection
    {
        return $this->morphedByMany(Exam::class, 'itemable')->withPivot([
            'number as number',
            "itemable_type as type"
        ])->orderByPivot('number');
    }

    /**
     * Attributes
     */
    /**
     * @return Lesson[]|Exam[]|Collection
     */
    public function getAllItemsAttribute(): Collection
    {
        return collect($this->lessons)->concat($this->exams)->sortBy('number');
    }
}
