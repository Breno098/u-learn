<?php

namespace App\Http\Requests\Admin\Quizz;

use App\Enums\AnswerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class QuizzStoreRequest extends FormRequest
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
            'answer_file' => 'nullable',
            "course_id" => 'required|exists:contents,id',
            "linkable_id" => 'nullable|integer',
            "linkable_type" => "nullable|in:content,season,chapter",
            "answer_file" => 'nullable',
            "questions" => 'nullable|array',
            "questions.*.id" => 'nullable|integer',
            "questions.*.title" => 'required|string',
            'questions.*.answer_type' => "required|in:" . implode(',', AnswerTypeEnum::toValues()),
            'questions.*.number' => 'nullable|integer',
            'questions.*.alternatives' => 'array|required_if:answer_type,' . AnswerTypeEnum::fechada()->value
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Preencha o nome',
            'course_id.required' => 'Vincule ao um conteúdo',
        ];
    }
}
