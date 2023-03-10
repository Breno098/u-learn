<?php

namespace App\Http\Requests\Admin\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
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
            'name' => 'nullable|string',
            'level' => 'nullable|string',
            'duration' => 'nullable|date_format:H:i',
            'video' => 'nullable|url',
            'url_sale' => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
            'certificate_id' => 'nullable|exists:certificates,id',
            'wallpaper_image' => 'nullable',
            'tumb_image' => 'nullable',
            'genres' => 'nullable|array',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
            'duration.date_format' => "Informe a data e hora corretamente. Exemplo: " . now()->format('H:i'),
        ];
    }
}
