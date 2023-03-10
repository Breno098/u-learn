<?php

namespace App\Http\Resources\Admin\Schedule;

use App\Models\Meeting;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Meeting */
        $meeting = $this->resource;

        return [
            'id' => $meeting->id,
            'name' => $meeting->name,
            'start_at' => $meeting->start_at ? $meeting->start_at->format('Y-m-d') : null,
            'type' => 'meeting',
            'number_of_students' => $meeting->number_of_students,
            'students_count' => $meeting->students->count(),
        ];
    }
}
