<?php

namespace App\Http\Requests\Admin\Content\Season;

use Illuminate\Foundation\Http\FormRequest;

class ContentSeasonUpdateRequest extends FormRequest
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
            'number' => 'required|integer',
            'image' => 'nullable',
            'number_of_chapters' => 'required|integer',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
            'number.required' => 'Preencha o numero da temporada',
        ];
    }
}
