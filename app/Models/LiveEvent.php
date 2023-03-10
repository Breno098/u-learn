<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property string $event_type
 * @property string $type
 * @property string $embed
 * @property string $image
 * @property int|null $responsible_id
 * @property int $number_of_students
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property Student[]|Collection $students
 * @property Group[]|Collection $groups
 * @property Campaign[]|Collection $campaigns
 * @property File[]|Collection $materials
 * @property User $responsible
 * @property Content $content
 * @property bool has_link_with_content
 * @property int $linkable_id
 * @property string $linkable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read array $student_ids
 * @property-read array $group_ids
 * @property-read array $participants_emails
 *
 * @method Builder filterLikeName(string|null $name)
 * @method Builder filterLikeDescription(string|null $description)
 * @method Builder filterType(string|null $type)
 * @method bool setLinkableTypeParse(string $type)
 * @method string getLinkableTypeParse()
 */
class LiveEvent extends Model
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
        'event_type',
        'type',
        'embed',
        'responsible_id',
        'number_of_students',
        'start_at',
        'end_at',
        'image',
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
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'has_link_with_content' => 'boolean'
    ];

    /**
     * Relantionships
     */
    /**
     * @return MorphTo
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Student[]|Collection|BelongsToMany
     */
    public function students(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * @return User|BelongsTo
     */
    public function responsible(): User|BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return Group[]|Collection|BelongsToMany
     */
    public function groups(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return Campaign[]|Collection|BelongsToMany
     */
    public function campaigns(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Campaign::class);
    }

    /**
     * @return Material[]|Collection|MorphMany
     */
    public function materials(): MorphMany|Collection
    {
        return $this->morphMany(Material::class, 'material');
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
    public function getStudentIdsAttribute(): array
    {
        return $this->students->pluck('id')->toArray();
    }

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
    public function getCampaignIdsAttribute(): array
    {
        return $this->campaigns->pluck('id')->toArray();
    }

    /**
     * @return array
     */
    public function getParticipantsEmailsAttribute(): array
    {
        $emails = [];

        foreach ($this->students ?? [] as $user) {
            $emails[] = $user->email;
        }

        foreach ($this->groups ?? [] as $group) {
            foreach ($group->students ?? [] as $user) {
                $emails[] = $user->email;
            }
        }

        return $emails;
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

    public function scopeFilterLikeDescription(Builder $builder, $description)
    {
        return $builder->when($description, function (Builder $builder, $description) {
            return $builder->where('description', 'like', "%{$description}%");
        });
    }

    public function scopeFilterType(Builder $builder, $type)
    {
        return $builder->when($type, function (Builder $builder, $type) {
            return $builder->where('type', $type);
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
