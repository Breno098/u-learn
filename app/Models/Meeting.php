<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $number_of_students
 * @property string $embed
 * @property string $live_offer
 * @property string $name_offer
 * @property string $embed_offer
 * @property string $image
 * @property string $tags
 * @property int|null $teacher_id
 * @property int $linkable_id
 * @property string $linkable_type
 * @property Content $content
 * @property bool has_link_with_content
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property User $teacher
 * @property Student[]|Collection $students
 * @property Group[]|Collection|BelongsToMany $groups
 * @property File[]|Collection $materials
 * @property-read array $student_ids
 * @property-read array $group_ids
 *
 * @method Builder filterLikeName(string|null $name)
 * @method bool setLinkableTypeParse(string $type)
 * @method string getLinkableTypeParse()
 */
class Meeting extends Model
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
        'type',
        'number_of_students',
        'start_at',
        'end_at',
        'live_offer',
        'name_offer',
        'description_offer',
        'embed_offer',
        'teacher_id',
        'image',
        'tags',
        'linkable_id',
        'linkable_type',
        'course_id',
        'has_link_with_content'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'number_of_students' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'live_offer' => 'boolean',
        'has_link_with_content' => 'boolean'
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
     * @return User
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return Student[]|Collection|BelongsToMany
     */
    public function students(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Student::class);
    }

     /**
     * @return Material[]|Collection|MorphMany
     */
    public function materials(): MorphMany
    {
        return $this->morphMany(Material::class, 'material');
    }

    /**
     * @return Group[]|Collection|BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return BelongsTo|Content
     */
    public function content(): BelongsTo|Content
    {
        return $this->belongsTo(Content::class)->withDefault([
            'has_season' => false
        ]);
    }

     /**
     * Attributes
     */

    /**
     * @return array
     */
    public function getGroupIdsAttribute(): array
    {
        return $this->groups->pluck('id')->toArray();
    }

     /**
     * @return array
     */
    public function getStudentIdsAttribute(): array
    {
        return $this->students->pluck('id')->toArray();
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

    public function scopeThatStartsAfterNow(Builder $builder)
    {
        return $builder->where('start_at', '>', now());
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
