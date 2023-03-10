<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self confeccao_propria()
 * @method static self outro()
 */
class ContentAuthorEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'confeccao_propria' => 'Confecção própria',
            'outro' => 'Outro',
        ];
    }
}
