<?php

namespace App\Http\Requests\Admin\Chapter;

use App\Enums\ChapterPlayerEnum;
use Illuminate\Foundation\Http\FormRequest;

class ChapterStoreRequest extends FormRequest
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
            'number' => 'nullable|integer',
            'duration' => 'nullable|date_format:H:i',
            'cast' => 'nullable|string',
            'direction' => 'nullable|string',
            'main_player' => "required|in:" . implode(',', ChapterPlayerEnum::toValues()),
            'vimeo_link' => 'nullable|url',
            'vimeo_embed' => 'nullable|string',
            'sambatech_link' => 'nullable|url',
            'sambatech_embed' => 'nullable|string',
            'image' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        $dateFormatForExample = now()->format('H:i');

        return [
            'name.required' => 'Preencha o nome',
            'duration.date_format' => "Informe a data corretamente. Exemplo: {$dateFormatForExample}",
        ];
    }
}
