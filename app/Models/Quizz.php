<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property int $attempts
 * @property string $answer_file
 * @property int $linkable_id
 * @property string $linkable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Question[]|Collection $questions
 * @property Content $content
 *
 * @method bool setLinkableTypeParse(string $type)
 * @method string getLinkableTypeParse()
 */
class Quizz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

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
        'linkable_id',
        'linkable_type',
        'content_id'
    ];

    /**
     * Relationships
     */
    /**
     * @return MorphTo
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

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
    /**
     * @return BelongsTo|Content
     */
    public function content(): BelongsTo|Content
    {
        return $this->belongsTo(Content::class)->withDefault();
    }

    /**
     * Helpers
     */
     /**
      * @return string
      */
    public function getLinkableTypeParse(): string
    {
        return match ($this->linkable_type) {
            Season::class => 'season',
            Chapter::class => 'chapter',
            Content::class => 'content',
            default => 'content'
        };
    }

    /**
     * @param string $type
     * @return bool
     */
    public function setLinkableTypeParse(string $type): bool
    {
        return $this->update([
            'linkable_type' => match ($type) {
                'season' => Season::class,
                'chapter' => Chapter::class,
                'content' => Content::class,
                default => Content::class
            }
        ]);
    }
}
