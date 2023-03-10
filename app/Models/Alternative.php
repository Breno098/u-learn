<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int|null $id
 * @property string $name
 * @property int $number
 * @property bool $is_correct
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Question $question
 */
class Alternative extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_correct',
        'number'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * @return Question|BelongsTo
     */
    public function question(): Question|BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
