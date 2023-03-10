<?php

namespace App\Http\Resources\Admin;

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         /** @var Group */
         $group = $this->resource;

        return [
            'id' => $group->id,
            'name' => $group->name,
            'permission_ids' => $group->permission_ids
        ];
    }
}
