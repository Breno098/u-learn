<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int|null $id
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ContentTag extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'content_id',
        'content_tag_id'
    ];

    /**
     * Relationships
     */
    /**
     * @return BelongsTo|Content
     */
    public function contentMain(): BelongsTo|Content
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * @return BelongsTo|Content
     */
    public function contentLink(): BelongsTo|Content
    {
        return $this->belongsTo(Content::class);
    }
}
