<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'display_to' => $this->display_to,
            'start_at' => $this->start_at ? $this->start_at->format('d/m/Y') : null,
            'end_at' => $this->end_at ? $this->end_at->format('d/m/Y') : null,
            'groups' => $this->group_ids,
            'students' => $this->student_ids,
        ];
    }
}
