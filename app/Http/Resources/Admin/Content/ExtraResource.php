<?php

namespace App\Http\Resources\Admin\Content;

use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\SectionResource;
use App\Models\Extra;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Extra */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'name' => $resource->name,
            'type' => $resource->type,
            'player' => $resource->player,
            'embed' => $resource->embed,
            'content_id' => $resource->content_id
        ];
    }
}
