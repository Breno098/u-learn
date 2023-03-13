<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property integer $number
 * @property Carbon $duration
 * @property string $wallpaper
 * @property string $video
 * @property boolean $can_comments
 * @property Module $module
 * @property Course $course
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Lesson extends Model
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
        'number',
        'duration',
        'video',
        'wallpaper',
        'can_comments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'datetime:H:i',
        'can_comments' => 'boolean'
    ];

    /**
     * @return Module|BelongsTo
     */
    public function module(): Module|BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
