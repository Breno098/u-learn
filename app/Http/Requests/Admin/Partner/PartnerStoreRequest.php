<?php

namespace App\Http\Requests\Admin\Partner;

use Illuminate\Foundation\Http\FormRequest;

class PartnerStoreRequest extends FormRequest
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
            'company_name' => 'required|string',
			'phone' => 'nullable|string',
            'start_at' => 'required|date_format:d/m/Y',
            'end_at' => 'nullable|date_format:d/m/Y',
            'link' => 'required|url',
            'email' => 'required|email',
            'image' => 'nullable|image'
        ];
    }

    public function messages()
    {
        $dateFormatForExample = now()->format('d/m/Y');

        return [
            'name.required' => 'Preencha o nome',
            'email.required' => 'Informe o email',
            'email.email' => 'Campo deve ser do tipo email',
            'company_name.required' => 'Informe a empresa',
            'start_at.required' => 'Informe a data e hora de início',
            'start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'end_at.required' => 'Informe a data e hora final',
            'end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'link.required' => 'Preencha o link',
            'link.url' => 'Deve ser do formato de url',
            'image.image' => 'Escolha um arquivo do tipo imagem'
        ];
    }
}
