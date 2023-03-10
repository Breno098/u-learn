<?php

namespace App\Http\Resources\Admin\Order;

use App\Http\Resources\Admin\CampaignResource;
use App\Http\Resources\Admin\StudentResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Order */
        $order = $this->resource;

        return [
            'id' => $order->id,
            'student' => new StudentResource($order->student),
            'campaign' => new CampaignResource($order->campaign),
            'campaign_id' => $order->campaign_id,
            'status' => $order->status,
            'payment_value' => $order->payment_value,
            'payment_method' => $order->payment_method,
            'term_accepted' => $order->term_accepted,
            'text_terms_acceptance' => $order->text_terms_acceptance,
            'registration_type' => $order->registration_type,
            'hiring_start_at' => $order->hiring_start_at ? $order->hiring_start_at->format('d/m/Y H:m') : null,
            'hiring_end_at' => $order->hiring_end_at ? $order->hiring_end_at->format('d/m/Y H:m') : null,
            'invoices' => $order->invoices,
        ];
    }
}
