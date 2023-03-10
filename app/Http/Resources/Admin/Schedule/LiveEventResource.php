<?php

namespace App\Http\Resources\Admin\Schedule;

use App\Models\LiveEvent;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var LiveEvent */
        $liveEvent = $this->resource;

        return [
            'id' => $liveEvent->id,
            'name' => $liveEvent->name,
            'start_at' => $liveEvent->start_at ? $liveEvent->start_at->format('Y-m-d') : null,
            'type' => 'liveEvent',
            'number_of_students' => $liveEvent->number_of_students,
            'students_count' => $liveEvent->students->count(),
        ];
    }
}
