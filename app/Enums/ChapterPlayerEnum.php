<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self vimeo()
 * @method static self sambatech()
 */
class ChapterPlayerEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'vimeo' => 'Vimeo',
            'sambatech' => 'Sambatech',
        ];
    }
}
