<?php

namespace App\Services\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @var string GUARD
     */
    const GUARD = 'admin';

    /**
     * @param array $data = []
     * @return void
     */
    public function login(array $data = []): void
    {
        if (! Auth::guard(self::GUARD)->attempt(Arr::only($data, ['email', 'password']))) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        Auth::guard(self::GUARD)->logout();
    }
}
