<?php

namespace App\Models;

use App\Enums\AnswerTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $id
 * @property string $title
 * @property string|AnswerTypeEnum $answer_type
 * @property int $number
 * @property string $video
 * @property string $audio
 * @property string $image
 * @property bool $has_video
 * @property bool $has_audio
 * @property bool $ihas_mage
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Alternative[]|Collection $alternatives
 */
class Question extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'answer_type',
        'number',
        'video',
        'audio',
        'image',
        'has_video',
        'has_audio',
        'has_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_video' => 'boolean',
        'has_audio' => 'boolean',
        'has_image' => 'boolean',
    ];

     /**
     *
     * @return HasMany|Alternative[]|Collection
     */
    public function alternatives(): HasMany|Collection
    {
        return $this->hasMany(Alternative::class)->orderBy('number');
    }
}
