<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Http\Requests\GoogleRequest\GoogleLoginRequest;
use App\Services\GoogleService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    protected GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

//    public function redirectToGoogle()
//    {
//        return Socialite::driver('google')->redirect();
//    }

//
//    public function handleGoogleCallback()
//    {
//        $user = Socialite::driver('google')->stateless()->user();
//        return $this->googleService->handleGoogleCallback($user);
//    }

    public function handleGoogleLogin(GoogleLoginRequest $googleLoginRequest)
    {
        $code= $googleLoginRequest->validated()['code'];

        try {
            // Lấy user từ mã code (sử dụng Socialite với driver stateless)
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->getAccessTokenResponse($code);

            $accessToken = $googleUser['access_token'];

            // Lấy thông tin người dùng từ Google bằng access token
            $userInfo = Socialite::driver('google')
                ->stateless()
                ->userFromToken($accessToken);

            return $this->googleService->handleGoogleLogin($userInfo);

        } catch (\Exception $e) {
            throw new AppException(ErrorCode::CODE_NOT_EMPTY);
        }

    }
}
