<?php

namespace App\Http\Requests\Admin\Quizz\Question;

use App\Enums\AnswerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends FormRequest
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
            'answer_type' => "required|in:" . implode(',', AnswerTypeEnum::toValues()),
            'number' => 'nullable|integer',
            'alternatives' => 'array|required_if:answer_type,' . AnswerTypeEnum::fechada()->value
        ];
    }

    public function messages()
    {
        $types = collect(AnswerTypeEnum::toLabels())->implode(', ', ' ou ');

        return [
            'title.required' => 'Preencha o título',
            'answer_type.required' => 'Escolha o tipo de resposta',
            'answer_type.in' => "O valor escolhido deve ser entre {$types}"
        ];
    }
}
