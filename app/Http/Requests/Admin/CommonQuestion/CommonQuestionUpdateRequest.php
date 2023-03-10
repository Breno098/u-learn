<?php

namespace App\Http\Requests\Admin\CommonQuestion;

use Illuminate\Foundation\Http\FormRequest;

class CommonQuestionUpdateRequest extends FormRequest
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
            'question_text' => 'required|string',
            'answer_text' => 'required|string',
            'show' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'question_text.required' => 'Preencha o a pergunta',
            'answer_text.required' => 'Preencha o a resposta',
        ];
    }
}
