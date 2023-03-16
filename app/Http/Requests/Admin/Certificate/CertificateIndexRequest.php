<?php

namespace App\Http\Requests\Admin\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class CertificateIndexRequest extends FormRequest
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
        ];
    }

    /**
     * Add prefix 'filters' before field messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
