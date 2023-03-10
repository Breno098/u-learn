<?php

namespace App\Http\Resources\Admin\Content;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ContentSummaryResource extends JsonResource
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
            'access_count' => $this->access_count,
            'launch_start_at' => $this->launch_start_at ? $this->launch_start_at->format('d/m/Y'): null,
            'expires_in' => $this->expiresIn($this->end_at)
        ];
    }

    /**
     * @param null|Carbon $end_at
     * @return int|null
     */
    public function expiresIn(null|Carbon $end_at): int|null
    {
        return $end_at ? $end_at->diffInDays() : null;
    }
}
