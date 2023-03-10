<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self agendamento()
 * @method static self grupo()
 * @method static self extra()
 * @method static self imersao()
 * @method static self individual()
 */
class MettingTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'agendamento' => 'Agendamento',
            'grupo' => 'Grupo',
            'extra' => 'Extra',
            'imersao' => 'Imersão',
            'individual' => 'Individual',
        ];
    }
}
