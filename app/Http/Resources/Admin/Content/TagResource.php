<?php

namespace App\Http\Resources\Admin\Content;

use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\SectionResource;
use App\Models\Content;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Content */
        $content = $this->resource;

        return [
            'id' => $content->id,
            'name' => $content->name,
        ];
    }
}
