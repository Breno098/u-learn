<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $id
 * @property string $name
 * @property string $company_name
 * @property string $phone
 * @property string $email
 * @property string $link
 * @property string $image
 * @property Carbon $start_at
 * @property Carbon $end_at
 *
 * @method Builder filterLikeName(string|null $name)
 */
class Partner extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'link',
        'start_at',
        'end_at',
        'image'
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
     * Relationships
     */

     /**
     * Scopes
     */
    public function scopeFilterLikeName(Builder $builder, $name)
    {
        return $builder->when($name, function (Builder $builder, $name) {
            return $builder->where('name', 'like', "%{$name}%");
        });
    }

}
