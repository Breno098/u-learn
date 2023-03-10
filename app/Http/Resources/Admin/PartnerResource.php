<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'link' => $this->link,
            'start_at' => $this->start_at ? $this->start_at->format('d/m/Y') : null,
            'end_at' => $this->end_at ? $this->end_at->format('d/m/Y') : null,
            'image' => $this->image,
        ];
    }
}
