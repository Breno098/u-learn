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
            'customer_cpf'  => $student->customer_cpf,
            'customer_phone'  => $student->customer_phone,
            'customer_address'  => $student->customer_address,
            'equal_data'  => $student->equal_data,

            /** Relations */
            'groups' => GroupResource::collection($student->groups),
            'group_ids' => $student->group_ids,

            ];
    }
}
