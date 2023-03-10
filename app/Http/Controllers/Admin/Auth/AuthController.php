<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Services\Admin\AuthService;
use Inertia\Response;

class AuthController extends Controller
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
        return inertia('Admin/Auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->authService->login($request->validated());

        return redirect()->intended(url()->previous());
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(): \Illuminate\Http\RedirectResponse
    {
        $this->authService->logout();

        return redirect('/');
    }
}
