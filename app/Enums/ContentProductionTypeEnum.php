<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self licenciamento()
 * @method static self parceria()
 */
class ContentProductionTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'licenciamento' => 'Licenciamento',
            'parceria' => 'Parceria',
        ];
    }
}
