<?php

namespace App\Http\Resources\Admin\Order;

use App\Http\Resources\Admin\CampaignResource;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSummaryResource extends JsonResource
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
            'student' => new UserResource($this->student),
            'campaign' => new CampaignResource($this->campaign),
            'status' => $this->status,
            'payment_value' => $this->payment_value,
            'payment_method' => $this->payment_method,
            'term_accepted' => $this->term_accepted,
            'text_terms_acceptance' => $this->text_terms_acceptance,
            'registration_type' => $this->registration_type,
            'hiring_start_at' => $this->hiring_start_at ? $this->hiring_start_at->format('d/m/Y H:m') : null,
            'hiring_end_at' => $this->hiring_end_at ? $this->hiring_end_at->format('d/m/Y H:m') : null,
            'invoices' => $this->invoices,
        ];
    }
}
