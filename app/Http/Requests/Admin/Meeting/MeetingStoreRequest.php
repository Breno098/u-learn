<?php

namespace App\Http\Requests\Admin\Meeting;

use App\Enums\MettingTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class MeetingStoreRequest extends FormRequest
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
            'type' => "required|in:" . implode(',', MettingTypeEnum::toValues()),
			'number_of_students'=> 'nullable|int',
			'start_at' => 'required|date_format:d/m/Y H:s',
            'end_at' => 'required|date_format:d/m/Y H:s',
            'teacher_id' => 'required|exists:users,id',
			'live_offer'=> 'nullable',
			'name_offer'=> 'nullable|string',
            'tags'=> 'nullable|string',
			'description_offer'=> 'nullable|string',
			'embed_offer'=> 'nullable|url',
            'materials' => 'nullable|array',
            'image' => 'nullable',
            'has_link_with_content' => 'required|boolean',
            "content_id" => 'required_if:has_link_with_content,true',
            "linkable_id" => 'required_unless:linkable_type,content',
            "linkable_type" => "nullable|in:content,season,chapter",
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        $dateFormatForExample = now()->format('d/m/Y H:s');

        $types = Arr::join(MettingTypeEnum::toLabels(), ', ', ' ou ');

        return [
            'name.required' => 'Preencha o nome',
            'type.required' => 'Selecione o tipo',
            'type.in' => "O encontro deve ser do tipo {$types}",
            'teacher_id.required' => 'Selecione o professor do encontro',
            'start_at.required' => 'Informe a data e hora de início',
            'start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'end_at.required' => 'Informe a data e hora final',
            'end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'embed_offer.url' => 'Deve ser do formato de url',
            'content_id.required_if' => 'Vincule a um conteúdo',
            'linkable_id.required_unless' => 'Vincule ao final do conteúdo ou selecione temporada ou episódio'
        ];
    }
}
