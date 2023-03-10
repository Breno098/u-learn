<?php

namespace App\Models;

use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderRegistrationTypeEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int|null $id
 * @property string $name
 * @property string $payment_method
 * @property string $payment_status
 * @property bool $term_accepted
 * @property string $text_terms_acceptance
 * @property Carbon $canceled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Team $team
 * @property Student $student
 * @property Campaign $campaign
 *
 * @method Builder paid()
 * @method Builder expired()
 * @method Builder canceled()
 * @method Builder notRenewed()
 */
class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'team_id',
        'campaign_id',
        'status',
        'payment_value',
        'payment_method',
        'term_accepted',
        'text_terms_acceptance',
        'registration_type',
        'hiring_start_at',
        'hiring_end_at',
        'canceled_at',
        'invoices',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'term_accepted' => 'boolean',
        'hiring_start_at' => 'datetime',
        'hiring_end_at' => 'datetime',
        'canceled_at' => 'datetime',
        // 'payment_method' => OrderPaymentMethodEnum::class,
        'status' => OrderStatusEnum::class,
        // 'registration_type' => OrderRegistrationTypeEnum::class,
    ];

    /**
     * @return Product[]|BelongsToMany|Collection
     */
    public function products(): BelongsToMany|Collection
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @return User
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withDefault();
    }

    /**
     * @return Campaign
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class)->withDefault();
    }

    /**
     * Scopes
     */
    public function scopePaid(Builder $builder)
    {
        return $builder->where('status', 'adimplente');
    }

    public function scopeExpired(Builder $builder)
    {
        return $builder->where('status', 'vencido');
    }

    public function scopeNotRenewed(Builder $builder)
    {
        return $builder->where('status', 'nao_renovou');
    }

    public function scopeCanceled(Builder $builder)
    {
        return $builder->where('status', 'cancelado');
    }
}
