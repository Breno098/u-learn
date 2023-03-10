<?php

namespace App\Http\Resources\Admin;

use App\Models\Alternative;
use App\Models\Question;
use App\Models\Quizz;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizzResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Quizz */
        $quizz = $this->resource;

        return [
            'id' => $quizz->id,
            'name' => $quizz->name,
            'description' => $quizz->description,
            'attempts' => $quizz->attempts,
            'answer_file' => $quizz->answer_file,

            'questions' => $quizz->questions->map(fn (Question $question) => [
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
            'content_id' => $quizz->content_id,
            'content' => [
                'id' => $quizz->content->id,
                'name' => $quizz->content->name,
                'has_seasons' => $quizz->content->has_seasons
            ],
            'linkable_type'=> $quizz->getLinkableTypeParse(),
            'linkable_id'=> $quizz->linkable_id,
        ];
    }
}
