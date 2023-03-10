<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int|null $id
 * @property string $cep
 * @property string $street
 * @property string $number
 * @property string $district
 * @property string $complement
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
class Address extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cep',
        'street',
        'number',
        'district',
        'complement',
    ];

    /**
     * @return User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
