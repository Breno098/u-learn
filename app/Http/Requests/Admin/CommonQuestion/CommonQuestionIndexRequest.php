<?php

namespace App\Http\Requests\Admin\CommonQuestion;

use Illuminate\Foundation\Http\FormRequest;

class CommonQuestionIndexRequest extends FormRequest
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
            'filters.question_text' => 'nullable|string',
            'filters.answer_text' => 'nullable|string',
        ];
    }

    /**
     * Add prefix 'filters' before field messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
