<?php

namespace App\Http\Requests\Admin\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class LessonStoreRequest extends FormRequest
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
            'description' => 'nullable|string',
            'number' => 'nullable|integer',
            'duration' => 'nullable|date_format:H:i',
            'direction' => 'nullable|string',
            'video' => 'nullable|url',
            'can_comments' => 'required|boolean',
            'wallpaper' => 'nullable',
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
