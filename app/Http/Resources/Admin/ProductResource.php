<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'url_sale' => $this->url_sale,
            'terms_acceptance' => $this->terms_acceptance,
            'image' => $this->image,
            'video_url' => $this->video_url,
            'credit_card_accept' => $this->credit_card_accept,
            'credit_card_value' => $this->credit_card_value,
            'credit_card_installments' => $this->credit_card_installments,
            'boleto_accept' => $this->boleto_accept,
            'boleto_value' => $this->boleto_value,
            'boleto_installments' => $this->boleto_installments,
            'pix_accept' => $this->pix_accept,
            'pix_value' => $this->pix_value,
            'checkout_code' => $this->checkout_code,
            'checkout_link' => $this->checkout_link,
        ];
    }
}
