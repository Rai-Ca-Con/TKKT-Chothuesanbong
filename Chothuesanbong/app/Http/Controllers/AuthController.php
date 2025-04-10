<?php

namespace App\Http\Controllers;

use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login() //on
    {
        $credentials = request(['email', 'password']);
        return $this->authService->login($credentials);
    }

    public function logout()
    {
        $refreshToken = request()->refresh_token;
        return $this->authService->logout($refreshToken);
    }

    // sd trong viec tao token moi va vo hieu hoa token cu
    public function refresh() //on
    {
        $accessToken = request()->access_token;
        $refreshToken = request()->refresh_token;
        return $this->authService->refresh($accessToken, $refreshToken);
    }

    public function profile()
    {
       return $this->authService->profile();
    }
}
