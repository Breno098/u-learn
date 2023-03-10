<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property int $size
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $storage_link
 */
class Material extends Model
{
    use HasFactory;

      /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'path',
        'extension',
        'size',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'storage_link',
        'is_image'
    ];


     /**
     * @return MorphTo
     */
    public function materialable()
    {
        return $this->morphTo();
    }

    /**
     * Attributes
     */
    public function getStorageLinkAttribute()
    {
        return Storage::url($this->path);
    }

    public function getIsImageAttribute()
    {
        return in_array($this->extension, ['png', 'jpg', 'jpeg']);
    }

    /**
     * Setters
     */
    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value;
        $this->attributes['extension'] = Arr::last(explode('.', $value));
    }
}
