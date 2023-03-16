<?php

namespace App\Http\Resources\Admin;

use App\Models\Certificate;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Certificate */
        $certificate = $this->resource;

        return [
            'id' => $certificate->id,
            'name' => $certificate->name,
            'image' => $certificate->image,
        ];
    }
}
