<?php

namespace App\Http\Resources\Admin;

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
            'description' => $liveEvent->description,
            'event_type' => $liveEvent->event_type,
            'type' => $liveEvent->type,
            'embed' => $liveEvent->embed,
            'number_of_students' => $liveEvent->number_of_students,
            'responsible_id' => $liveEvent->responsible_id,
            'start_at' => $liveEvent->start_at ? $liveEvent->start_at->format('d/m/Y H:i') : null,
            'end_at' => $liveEvent->end_at ? $liveEvent->end_at->format('d/m/Y H:i') : null,
            'group_ids' => $liveEvent->group_ids,
            'student_ids' => $liveEvent->student_ids,
            'campaign_ids' => $liveEvent->campaign_ids,
            'responsible' => $liveEvent->responsible,
            'image' => $liveEvent->image,
            'materials' => MaterialResource::collection($liveEvent->materials),
            'has_link_with_content' => $liveEvent->has_link_with_content,
            'content_id' => $liveEvent->content_id,
            'content' => [
                'id' => $liveEvent->content->id,
                'name' => $liveEvent->content->name,
                'has_seasons' => $liveEvent->content->has_seasons
            ],
            'linkable_type'=> $liveEvent->getLinkableTypeParse(),
            'linkable_id'=> $liveEvent->linkable_id,
        ];
    }
}
