<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $title
 * @property string $description
 * @property string $link
 * @property bool $display_to
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Student[]|Collection $students
 * @property Group[]|Collection $groups
 * @property-read array $student_ids
 * @property-read array $groups_ids
 *
 * @method Builder filterLikeTitle(string|null $name)
 * @method Builder filterDisplayTo(string|null $displayTo)
 * @method Builder filterBiggerOrEqualStartAt(string|null $startAt, string $format)
 * @method Builder filterLessOrEqualStartAt(string|null $startAt, string $format)
 */
class JobVacancy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'link',
        'start_at',
        'end_at',
        'display_to',
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
     * Relantionships
     */
     /**
     * @return Student[]|Collection|BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * @return Group[]|Collection
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
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
     * Scopes
     */
    public function scopeFilterLikeTitle(Builder $builder, $title)
    {
        return $builder->when($title, function (Builder $builder, $title) {
            return $builder->where('title', 'like', "%{$title}%");
        });
    }

    public function scopeFilterDisplayTo(Builder $builder, $displayTo)
    {
        return $builder->when($displayTo, function (Builder $builder, $displayTo) {
            return $builder->where('display_to', $displayTo);
        });
    }

    public function scopeFilterBiggerOrEqualStartAt(Builder $builder, $startAt, $format = 'd/m/Y')
    {
        return $builder->when($startAt, function (Builder $builder, $startAt) use ($format){
            $startAt = $startAt instanceof Carbon ? $startAt : Carbon::createFromFormat($format, $startAt);

            return $builder->where('start_at', '>=', $startAt->startOfDay());
        });
    }

    public function scopeFilterLessOrEqualStartAt(Builder $builder, $startAt, $format = 'd/m/Y')
    {
        return $builder->when($startAt, function (Builder $builder, $startAt) use ($format){
            $startAt = $startAt instanceof Carbon ? $startAt : Carbon::createFromFormat($format, $startAt);

            return $builder->where('start_at', '>=', $startAt->startOfDay());
        });
    }
}
