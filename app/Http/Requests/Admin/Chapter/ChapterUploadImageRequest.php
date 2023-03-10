<?php

namespace App\Http\Requests\Admin\Chapter;

use Illuminate\Foundation\Http\FormRequest;

class ChapterUploadImageRequest extends FormRequest
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
            'image' => 'nullable|image'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'image.image' => 'Escolha um arquivo do tipo imagem'
        ];
    }
}
