<?php

namespace App\Http\Requests\Admin\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
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
            'email' => 'required|email|unique:students,email',
            'active' => 'nullable|boolean',
            'cpf' => 'nullable|string',
            'phone' => 'nullable',
            'address' => 'nullable|string',
            'password' => 'nullable|string',
            'customer_cpf' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'equal_data' => 'nullable|integer',
            'group_ids' => 'nullable|array',

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
            'email.required' => 'Preencha o email',
            'email.email' => 'Insira um email válido',
            'email.unique' => 'O email já está sendo utilizado por outro usuário',
            'customer_cpf.required' => 'Preencha o cpf do cliente',
            'customer_phone.required' => 'Preencha o telefone do cliente',
            'customer_address.required' => 'Preencha o endereço do cliente',
        ];
    }
}
