<?php

namespace App\Http\Resources\Admin;

use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Lesson */
        $lesson = $this->resource;

        return [
            'id' => $lesson->id,
            'name' => $lesson->name,
            'number' => $lesson->number,
            'duration' => $lesson->duration ? $lesson->duration->format('H:s') : null,
            'wallpaper' => $lesson->wallpaper,
            'video' => $lesson->video,
            'release_type' => $lesson->release_type,
            'can_comments' => $lesson->can_comments,
        ];
    }
}