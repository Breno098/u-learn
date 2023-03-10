<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self adimplente()
 * @method static self cancelado()
 * @method static self nao_renovou()
 * @method static self vencido()
 */
class OrderStatusEnum extends Enum
{
    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'adimplente' => 'Adimplente',
            'cancelado' => 'Cancelado',
            'nao_renovou' => 'Não renovou',
            'vencido' => 'Vencido',
        ];
    }
}
