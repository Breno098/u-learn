<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image'
    ];

    /**
     * Relationships
     */
    /**
     * @return HasOne|Course
     */
    public function course(): HasOne|Course
    {
        return $this->hasOne(Course::class)->withDefault();
    }
}
