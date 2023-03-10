<?php

namespace App\Services\App\Auth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    /**
     * @var string GUARD
     */
    const GUARD = 'app';

    /**
     * @param array $data = []
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function handle(array $data = []): array
    {
        $auth = Auth::guard(self::GUARD)->attempt(Arr::only($data, ['email', 'password']));

        if (!$auth) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::guard(self::GUARD)->user();

        return [
            'user' => $user,
            'token' => $user->createToken(self::GUARD . "-{$data['device_name']}")->plainTextToken
        ];
    }
}
