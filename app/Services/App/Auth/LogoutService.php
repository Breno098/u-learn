<?php

namespace App\Services\App\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutService
{
    /**
     * @var string GUARD
     */
    const GUARD = 'app';

    /**
     * @return array
     */
    public function handle(): array
    {
        Auth::guard(self::GUARD)->logout();

        request()->user()->currentAccessToken()->delete();

        return [
            'logout' => true,
        ];
    }
}
