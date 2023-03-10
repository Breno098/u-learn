<?php

namespace App\Http\Requests\Admin\Meeting;

use App\Enums\MettingTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class MeetingIndexRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Add prefix 'filters' before field rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filters.name' => 'nullable|string',
            'filters.type' => "nullable|in:" . implode(',', MettingTypeEnum::toValues()),
			'filters.start_at' => 'nullable|date_format:d/m/Y H:s',
            'filters.end_at' => 'nullable|date_format:d/m/Y H:s',
            'filters.teacher_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Add prefix 'filters' before field messages
     *
     * @return array
     */
    public function messages(): array
    {
        $dateFormatForExample = now()->format('d/m/Y H:s');

        return [
            'filters.type.in' => 'O evento deve ser do tipo Individual,Grupo,Extra,Imersão ou Agendamento',
            'filters.start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
            'filters.end_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
        ];
    }
}
