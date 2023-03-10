<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\PasswordSendLinkRequest;
use App\Services\Customer\AuthService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordSendLinkController extends Controller
{
    /**
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return Response
     */
    public function form(): Response
    {
        return inertia('Customer/Auth/ForgotPassword');
    }

    /**
     * @param PasswordSendLinkRequest $request
     * @return Response
     */
    public function sendLink(PasswordSendLinkRequest $request): Response
    {
        $responseOk = $this->authService->passwordSendLink($request->get('email'));

        if ($responseOk) {
            return inertia('Customer/Auth/ForgotPassword', [
                'status' =>"Email para redefinição enviado para {$request->get('email')}"
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'Erro ao enviar email. Tente novamente.',
        ]);
    }
}
