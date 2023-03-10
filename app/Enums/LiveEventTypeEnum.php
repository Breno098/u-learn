<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self individual()
 * @method static self grupo()
 */
class LiveEventTypeEnum extends Enum
{
     /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'individual' => 'Individual',
            'grupo' => 'Grupo',
        ];
    }
}
