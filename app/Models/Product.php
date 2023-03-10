<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $id
 * @property string $name
 * @property string $description
 * @property string $url_sale
 * @property string $terms_acceptance
 * @property string $image
 * @property string $video_url
 * @property bool $credit_card_accept
 * @property int $credit_card_value
 * @property int $credit_card_installments
 * @property bool $boleto_accept
 * @property int $boleto_value
 * @property int $boleto_installments
 * @property bool $pix_accept
 * @property int $pix_value
 * @property string $checkout_code
 * @property string $checkout_link
 * @property ProductInstallment[]|Collection|HasMany $installments
 * @property-read string $credit_card_value_currency (R$ {credit_card_value} / 100)
 * @property-read string $boleto_value_currency (R$ {boleto_value} / 100)
 * @property-read string $pix_value_currency (R$ {pix_value} / 100)
 *
 * @method Builder filterLikeName(string|null $name)
 */
class Product extends Model
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
        'url_sale',
        'terms_acceptance',
        'image',
        'video_url',
        'credit_card_accept',
        'credit_card_value',
        'credit_card_installments',
        'boleto_accept',
        'boleto_value',
        'boleto_installments',
        'pix_accept',
        'pix_value',
        'checkout_code',
        'checkout_link',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credit_card_value' => 'float',
        'credit_card_accept' => 'boolean',
        'boleto_value' => 'float',
        'boleto_accept' => 'boolean',
        'pix_value' => 'float',
        'pix_accept' => 'boolean',
    ];

    /**
     * Relationships
     */
    /**
     * @return ProductInstallment[]|Collection|HasMany
     */
    public function installments(): HasMany
    {
        return $this->hasMany(ProductInstallment::class);
    }

    /**
     * Attributes
     */

    /**
     * @return string
     */
    public function getCreditCardValueCurrencyAttribute(): string
    {
        return 'R$ ' . number_format($this->credit_card_value, 2, ",", ".");
    }

     /**
     * @return string
     */
    public function getBoletoValueCurrencyAttribute(): string
    {
        return 'R$ ' . number_format($this->boleto_value, 2, ",", ".");
    }

     /**
     * @return string
     */
    public function getPixValueCurrencyAttribute(): string
    {
        return 'R$ ' . number_format($this->pix_value, 2, ",", ".");
    }

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
