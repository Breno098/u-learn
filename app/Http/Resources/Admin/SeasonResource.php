<?php

namespace App\Http\Resources\Admin;

use App\Models\Season;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Season */
        $season = $this->resource;

        return [
            'id' => $season->id,
            'name' => $season->name,
            'number' => $season->number,
            'title' => $season->title,
            'start_at' => $season->start_at ? $season->start_at->format('d/m/Y') : null,
            'end_at' => $season->end_at ? $season->end_at->format('d/m/Y') : null,
            'image' => $season->image,
            'number_of_chapters' => $season->number_of_chapters,
            'chapters' => ChapterResource::collection($season->chapters),
            'count_chapters'=> $season->chapters->count(),
        ];
    }
}
