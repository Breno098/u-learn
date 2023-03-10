<?php

namespace App\Http\Requests\Admin\JobVacancy;

use Illuminate\Foundation\Http\FormRequest;

class JobVacancyStoreRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'nullable|string',
			'display_to' => 'required|in:Usuário,Grupo',
            'students' => 'nullable|array',
            'groups' => 'nullable|array',
            'start_at' => 'required|date_format:d/m/Y',
            'end_at' => 'nullable|date_format:d/m/Y',
            'link' => 'required|url'
        ];
    }

    public function messages()
    {
        $dateFormatForExample = now()->format('d/m/Y');

        return [
            'title.required' => 'Preencha o nome',
            'display_to.required' => 'Selecione o tipo',
            'type.in' => 'O evento deve ser do tipo Usuário ou em Grupo',
            'responsible_id.required' => 'Selecione o responsável pelo evento',
            'start_at.required' => 'Informe a data e hora de início',
            'start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'link.required' => 'Preencha o link',
            'link.url' => 'Deve ser do formato de url',
        ];
    }
}
