<?php

namespace App\Http\Resources\Admin;

use App\Models\Student;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Student */
        $student = $this->resource;

        return [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'active' => $student->active,
            'cpf' => $student->cpf,
            'phone' => $student->phone,
            'address'  => $student->address,
            'groups' => GroupResource::collection($student->groups),
            'group_ids' => $student->group_ids,
        ];
    }
}
