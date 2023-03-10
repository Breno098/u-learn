<?php

namespace App\Http\Resources\Admin;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         /** @var Role */
         $role = $this->resource;

        return [
            'id' => $role->id,
            'name' => $role->name,
            'permission_ids' => $role->permission_ids
        ];
    }
}
