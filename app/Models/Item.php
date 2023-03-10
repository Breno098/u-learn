<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property int $campaign_id
 * @property Campaign $campaign
 */
class Item extends Model
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
        'image',
        'campaign_id'
    ];

    /**
     * @return Campaign|BelongsTo
     */
    public function campaign(): Campaign|BelongsTo
    {
        return $this->belongsTo(Campaign::class)->withDefault();
    }
}
