<?php

namespace App\Http\Resources\Admin;

use App\Models\Alternative;
use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Exam */
        $exam = $this->resource;

        return [
            'id' => $exam->id,
            'name' => $exam->name,
            'description' => $exam->description,
            'attempts' => $exam->attempts,
            'answer_file' => $exam->answer_file,
            'questions' => $exam->questions->map(fn (Question $question) => [
                'id' => $question->id,
                'title' => $question->title,
                'text' => $question->text,
                'answer_type' => $question->answer_type,
                'number' => $question->number,
                'video' => $question->video,
                'audio' => $question->audio,
                'image' => $question->image,
                'has_video' => $question->has_video,
                'has_audio' => $question->has_audio,
                'has_image' => $question->has_image,
                'alternatives' => $question->alternatives->map(fn (Alternative $alternative) => [
                    'name' => $alternative->name
                ])
            ]),
            /** Pivot Relation */
            'number' => $this->whenNotNull($exam->number),
            'type' => $this->whenNotNull($exam->type),
        ];
    }
}
