<?php

namespace App\Http\Requests\Admin\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class CertificateStoreRequest extends FormRequest
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
            'image' => 'nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
        ];
    }
}
