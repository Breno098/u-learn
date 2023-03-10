<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\PasswordSendLinkRequest;
use App\Models\User;
use App\Services\Admin\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordSendLinkController extends Controller
{
    /**
     * @var PasswordService
     */
    protected PasswordService $passwordService;

    /**
     * @param PasswordService $passwordService
     */
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @return Response
     */
    public function form(): Response
    {
        return inertia('Admin/Auth/ForgotPassword');
    }

    /**
     * @param PasswordSendLinkRequest $request
     * @return RedirectResponse
     */
    public function sendLink(PasswordSendLinkRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->get('email'))->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'E-mail não encontrado.',
            ]);
        }

        if (! $this->passwordService->sendResetPasswordMail($user)) {
            throw ValidationException::withMessages([
                'send_reset_password_mail' => 'Erro ao enviar email. Tente novamente.',
            ]);
        }

        return redirect()->back();
    }
}
