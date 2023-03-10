<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self content()
 * @method static self season()
 * @method static self chapter()
 */
class LinkableTypeEnum extends Enum
{
    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'content' => 'No final do conteúdo',
            'season' => 'Em uma temporada',
            'chapter' => 'Em um episódio',
        ];
    }
}
