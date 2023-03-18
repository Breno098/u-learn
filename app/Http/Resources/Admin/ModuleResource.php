<?php

namespace App\Http\Resources\Admin;

use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Module */
        $module = $this->resource;

        return [
            'id' => $module->id,
            'name' => $module->name,
            'number' => $module->number,
            'image' => $module->image,
            'description' => $module->description,
            'all_items' => $module->all_items->map(function (Lesson|Exam $item) {
                return $item instanceof Exam ?  new ExamResource($item) : new LessonResource($item);
            })->toArray()
        ];
    }
}
