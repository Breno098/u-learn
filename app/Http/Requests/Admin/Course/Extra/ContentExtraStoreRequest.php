<?php

namespace App\Http\Requests\Admin\Content\Extra;

use App\Enums\ExtraPlayerEnum;
use App\Enums\ExtraTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ContentExtraStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'type' => "required|in:" . implode(',', ExtraTypeEnum::toValues()),
            'player' => "required|in:" . implode(',', ExtraPlayerEnum::toValues()),
            'embed' => 'nullable|url',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        $extraTypeEnum = collect(ExtraTypeEnum::toLabels())->implode(', ', ' ou ');
        $extraPlayerEnum = collect(ExtraPlayerEnum::toLabels())->implode(', ', ' ou ');

        return [
            'name.required' => 'Preencha o nome',
            'type.required' => 'Escolha o tipo',
            'type.in' => "O valor escolhido deve ser entre {$extraTypeEnum}",
            'player.required' => 'Escolha o player',
            'player.in' => "O valor escolhido deve ser entre {$extraPlayerEnum}",
            'embed.url' => "Digite um link válido"
        ];
    }
}
