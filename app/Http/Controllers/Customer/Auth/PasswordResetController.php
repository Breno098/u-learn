<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\PasswordResetRequest;
use App\Services\Customer\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
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
     * @param Request $request
     * @return Response
     */
    public function form(Request $request): Response
    {
        return inertia('Customer/Auth/ResetPassword', [
            'token' => $request->route('token'),
            'email' => $request->get('email')
        ]);
    }

    /**
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(PasswordResetRequest $request): \Illuminate\Http\RedirectResponse
    {
        $responseOk = $this->authService->passwordReset(
            $request->get('email'),
            $request->get('password'),
            $request->get('password_confirmation'),
            $request->get('token'),
        );

        if ($responseOk) {
            return redirect()
                ->route('customer.auth.sign-in')
                ->with('status',"Senha redefinida com sucesso.");
        }

        throw ValidationException::withMessages([
            'email' => 'Erro ao redefinir a senha.',
        ]);
    }
}
