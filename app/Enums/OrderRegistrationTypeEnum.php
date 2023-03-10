<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self anual()
 * @method static self mensal()
 */
class OrderRegistrationTypeEnum extends Enum
{
    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'anual' => 'Anual',
            'mensal' => 'Mensal',
        ];
    }
}
