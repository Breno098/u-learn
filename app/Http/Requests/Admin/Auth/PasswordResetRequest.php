<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetRequest extends FormRequest
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
            'token' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'O email não foi encontrado',
            'password.confirmed' => 'Senhas não conferem.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'password.required' => 'Digite um senha'
        ];
    }
}
