<?php

namespace App\Http\Requests\Admin\LiveEvent;

use App\Enums\LiveEventTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class LiveEventStoreRequest extends FormRequest
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
            'type' => "required|in:" . implode(',', LiveEventTypeEnum::toValues()),
            'student_ids' => 'nullable|array',
            'group_ids' => 'nullable|array',
            'campaign_ids' => 'nullable|array',
            'number_of_students' => 'required|integer',
            'responsible_id' => 'required|exists:users,id',
            'start_at' => 'required|date_format:d/m/Y H:i|after:'.now()->addMinute(1)->format('d/m/Y H:i').'|before:end_at',
            'end_at' => 'required|date_format:d/m/Y H:i|after:start_at',
            'embed' => 'required|url',
            'materials' => 'nullable|array',
            'image' => 'nullable',
            'has_link_with_content' => 'required|boolean',
            "content_id" => 'required_if:has_link_with_content,true',
            "linkable_id" => 'required_unless:linkable_type,content',
            "linkable_type" => "nullable|in:content,season,chapter",
        ];
    }

    public function messages()
    {
        $dateFormatForExample = now()->format('d/m/Y H:i');

        $dateFormatMoreOneMinute = now()->addMinute(1)->format('d/m/Y H:i');

        $types = Arr::join(LiveEventTypeEnum::toLabels(), ', ', ' ou ');

        return [
            'name.required' => 'Preencha o nome',
            'type.required' => 'Selecione o tipo',
            'type.in' => "O evento deve ser do tipo {$types}",
            'responsible_id.required' => 'Selecione o responsável pelo evento',
            'start_at.required' => 'Informe a data e hora de início',
            'start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'start_at.after' => "Informe a data e hora de início posterior a {$dateFormatMoreOneMinute}",
            'start_at.before' => 'Informe a data e hora de início anterior a data e hora de término',
            'end_at.required' => 'Informe a data e hora final',
            'end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'end_at.after' => 'Informe a data e hora de término posterior a data e hora de início',
            'embed.required' => 'Preencha o link',
            'embed.url' => 'Deve ser do formato de url',
            'content_id.required_if' => 'Vincule a um conteúdo',
            'linkable_id.required_unless' => 'Vincule ao final do conteúdo ou selecione temporada ou episódio'
        ];
    }
}
