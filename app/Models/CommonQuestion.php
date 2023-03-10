<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $id
 * @property string $question_text
 * @property string $answer_text
 * @property bool $show
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CommonQuestion extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_text',
        'answer_text',
        'show',
    ];

    protected $casts = [
        'show' => 'boolean',
    ];
}
