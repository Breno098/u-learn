<?php

namespace App\Http\Requests\Admin\Order;

use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Add prefix 'filters' before field rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filters.name' => 'nullable|string',
            'filters.payment_method' => "nullable|in:" . implode(',', OrderPaymentMethodEnum::toValues()),
            'filters.status' => "nullable|in:" . implode(',', OrderStatusEnum::toValues()),
			'filters.hiring_start_at' => 'nullable|date_format:d/m/Y',
            'filters.campaign_id' => 'nullable|exists:campaigns,id',
            'filters.student_id' => 'nullable|exists:students,id',
        ];
    }

    /**
     * Add prefix 'filters' before field messages
     *
     * @return array
     */
    public function messages(): array
    {
        $dateFormatForExample = now()->format('d/m/Y H:s');

        return [
            'filters.hiring_start_at.date_format' => "Informe a data e hora corretamente. Exemplo: {$dateFormatForExample}",
        ];
    }
}
