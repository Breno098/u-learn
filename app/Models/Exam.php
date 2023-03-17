<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property int $attempts
 * @property string $answer_file
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Question[]|Collection $questions
 */
class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'attempts',
        'answer_file',
        'course_id'
    ];

    /**
     * Relationships
     */

    /**
     *
     * @return HasMany|Question[]|Collection
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('number');
    }

     /**
     * Relationships
     */
}
