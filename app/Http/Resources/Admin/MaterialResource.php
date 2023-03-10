<?php

namespace App\Http\Resources\Admin;

use App\Models\Material;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Material */
        $material = $this->resource;

        return [
            'id' => $material->id,
            'name' => $material->name,
            'link' => $material->path,
            'extension' => $material->extension,
            'size' => $material->size,
            'is_image' => $material->is_image
        ];
    }
}
