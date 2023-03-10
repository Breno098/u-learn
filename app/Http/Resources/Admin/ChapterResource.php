<?php

namespace App\Http\Resources\Admin;

use App\Models\Chapter;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Chapter */
        $resourse = $this->resource;

        return [
            'id' => $resourse->id,
            'name' => $resourse->name,
            'number' => $resourse->number,
            'duration' => $resourse->duration ? $resourse->duration->format('H:s') : null,
            'cast' => $resourse->cast,
            'direction' => $resourse->direction,
            'image' => $resourse->image,
            'main_player' => $resourse->main_player,
            'vimeo_link' => $resourse->vimeo_link,
            'vimeo_embed' => $resourse->vimeo_embed,
            'sambatech_link' => $resourse->sambatech_link,
            'sambatech_embed' => $resourse->sambatech_embed,
        ];
    }
}
