<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => "required|email|unique:users,email,{$this->user->id}",
            'password' => 'nullable|confirmed',
            'active' => 'nullable|boolean',
            'cpf' => 'nullable|string',
            'phone' => 'nullable',
            'address' => 'nullable|string',
            'role_ids' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Preencha o nome',
            'email.required' => 'Preencha o email',
            'email.email' => 'Insira um email válido',
            'email.unique' => 'O email já está sendo utilizado por outro usuário',
        ];
    }
}
