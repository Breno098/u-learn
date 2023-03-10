<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self boleto()
 * @method static self cartao()
 * @method static self pix()
 */
class OrderPaymentMethodEnum extends Enum
{
    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'boleto' => 'Boleto',
            'cartao' => 'Cartão de crédito',
            'pix' => 'PIX',
        ];
    }
}
