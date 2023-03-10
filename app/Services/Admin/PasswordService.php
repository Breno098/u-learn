<?php

namespace App\Services\Admin;

use App\Mail\Admin\ResetPasswordMail;
use App\Mail\Admin\UserCreatedMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordService
{
    /**
     * @param User $user
     * @return string
     */
    private function generateToken(User $user): string
    {
        return app('auth.password.broker')->createToken($user);
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function sendResetPasswordMail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $this->generateToken($user)));

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

     /**
     * @param User $user
     * @return boolean
     */
    public function sendCreatedMail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $this->generateToken($user)));

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function passwordReset(string $email, string $password, string $password_confirmation, string $token)
    {
        $status = Password::broker('admins')->reset([
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
                'token' => $token
            ],
            function ($user, $password){
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::guard('admin')->login($user);
            }
        );

        return $status == Password::RESET_LINK_SENT;
    }
}
