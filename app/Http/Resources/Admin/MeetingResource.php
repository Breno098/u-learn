<?php

namespace App\Http\Resources\Admin;

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
            'description' => $meeting->description,
            'type' => $meeting->type,
            'number_of_students' => $meeting->number_of_students,
            'start_at' => $meeting->start_at ? $meeting->start_at->format('d/m/Y H:i') : null,
            'end_at' => $meeting->end_at ? $meeting->end_at->format('d/m/Y H:i') : null,
            'live_offer' => $meeting->live_offer,
            'name_offer' => $meeting->name_offer,
            'description_offer' => $meeting->description_offer,
            'embed_offer' => $meeting->embed_offer,
            'teacher_id' => $meeting->teacher_id,
            'student_id' => $meeting->student_id,
            'groups' => $meeting->group_ids,
            'teacher' => $meeting->teacher,
            'image' => $meeting->image,
            'tags' => $meeting->tags,
            'materials' => MaterialResource::collection($meeting->materials),
            'has_link_with_content' => $meeting->has_link_with_content,
            'content_id' => $meeting->content_id,
            'content' => [
                'id' => $meeting->content->id,
                'name' => $meeting->content->name,
                'has_seasons' => $meeting->content->has_seasons
            ],
            'linkable_type'=> $meeting->getLinkableTypeParse(),
            'linkable_id'=> $meeting->linkable_id,
        ];
    }
}
