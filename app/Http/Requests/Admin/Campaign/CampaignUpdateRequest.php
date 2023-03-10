<?php

namespace App\Http\Requests\Admin\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class CampaignUpdateRequest extends FormRequest
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
            'start_at' => 'nullable|date_format:d/m/Y',
            'end_at' => 'nullable|date_format:d/m/Y',
        ];
    }

    public function messages()
    {
        $dateFormatForExample = fake()->dateTimeBetween('-10 years', 'now')->format('d/m/Y');

        return [
            'name.required' => 'Preencha o nome',
            'start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
        ];
    }
}
