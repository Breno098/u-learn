<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self aberta()
 * @method static self fechada()
 * @method static self imagem()
 * @method static self video()
 * @method static self audio()
 */
class AnswerTypeEnum extends Enum
{
    /**
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'aberta' => 'Aberta',
            'fechada' => 'Múltipla escolha',
            'imagem' => 'Imagem',
            'video' => 'Vídeo',
            'audio' => 'Áudio',
        ];
    }
}
