<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest  extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description'=> 'nullable|string',
            'url_sale'=> 'nullable|string',
            'terms_acceptance'=> 'nullable|string',
            'video_url'=> 'nullable|url',
            'credit_card_accept'=> 'required',
            'credit_card_value'=> 'required',
            'credit_card_installments'=> 'nullable',
            'boleto_accept'=> 'required',
            'boleto_value'=> 'required',
            'boleto_installments'=> 'nullable',
            'pix_accept'=> 'required',
            'pix_value'=> 'required',
            'checkout_code' => 'nullable|string',
            'checkout_link' => 'nullable|url',
            'image' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
            'url_sale.url' => 'O link de venda deve ser uma url',
            'video_url.url' => 'O link do video deve ser uma url',
            'checkout_link.url' => 'O link do checkout deve ser uma url',
            'credit_card_value.required'=> 'Informe um valor para a compra com o cartão de crédito',
            'boleto_value.required'=> 'Informe um valor para a compra com o boleto',
            'pix_value.required'=> 'Informe um valor para a compra com o PIX',
        ];
    }
}
