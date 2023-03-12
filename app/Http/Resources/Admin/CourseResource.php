<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\GenreResource;
use App\Models\Course;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Course */
        $course = $this->resource;

        return [
            'id' => $course->id,
            'name' => $course->name,
            'descritiption' => $course->descritiption,
            'category_id' => $course->category_id,
            'category' => new CategoryResource($course->category),
            'level' => $course->level,
            'duration' => $course->duration ? $course->duration->format('H:i') : null,
            'video' => $course->video,
            'url_sale' => $course->url_sale,
            'category_id' => $course->category_id,
            'certificate_id' => $course->certificate_id,
            'wallpaper_image' => $course->wallpaper_image,
            'tumb_image' => $course->tumb_image,
            'genres_ids'=> $course->genres_ids,
            'genres' => GenreResource::collection($course->genres),
        ];
    }
}
