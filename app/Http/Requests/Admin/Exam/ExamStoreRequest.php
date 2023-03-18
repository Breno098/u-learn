<?php

namespace App\Http\Requests\Admin\Exam;

use App\Enums\AnswerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ExamStoreRequest extends FormRequest
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
            "questions" => 'nullable|array',
            "questions.*.id" => 'nullable|integer',
            "questions.*.title" => 'required|string',
            'questions.*.answer_type' => "required|in:" . implode(',', AnswerTypeEnum::toValues()),
            'questions.*.number' => 'nullable|integer',
            'questions.*.alternatives' => 'array|required_if:answer_type,' . AnswerTypeEnum::fechada()->value,
            'questions.*.number' => 'nullable|integer',
            'questions.*.has_video' => 'required|boolean',
            'questions.*.video' => 'required_if:has_video,true',
            'questions.*.has_audio' => 'required|boolean',
            'questions.*.audio' => 'required_if:has_audio,true',
            'questions.*.has_image' => 'required|boolean',
            'questions.*.image' => 'required_if:has_image,true',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
        ];
    }
}
