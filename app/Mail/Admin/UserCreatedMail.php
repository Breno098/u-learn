<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

   /**
     * @var User
     */
    public User $user;

    /**
     * @param string $token
     */
    public string $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $token
     * @return void
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $parameters = [
            'token' => $this->token,
            'email' => $this->user->email
        ];

        $url = url(config('app.url').route('admin.password.reset', $parameters, false));

        return $this->subject('Bem vindo')
            ->markdown('admin.mail.password.setting', [
                'url' => $url,
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
            ]);
    }
}
