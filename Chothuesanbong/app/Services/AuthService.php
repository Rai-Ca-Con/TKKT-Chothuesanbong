<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($credentials) //on
    {
        if (!$token = auth()->attempt($credentials)) { // neu dung $token se chua gia tri token
            throw new AppException(ErrorCode::INCORRECT_LOGIN_INFO);
        }

        $refreshToken = $this->createRefreshToken(); // tao rf token
        return $this->respondWithToken($token, $refreshToken); // tra ve token va rf token
    }

    public function logout($refreshToken)
    {
        $userId = auth()->user()->id;
        auth()->logout(); //dua token vao blacklist va token k sd dc nx
        //thu hoi refresh_token
        if ($refreshToken) {
            $this->userRepository->update($userId, ['refresh_token' => '']);
        }
        return response()->json(['message' => 'Đăng xuất thành công!']);
    }


    // lay thong tin chji tiet user thong qua token
    public function profile()
    {
        try {
            return response()->json(auth()->user());
        } catch (JWTException $ex) {
            throw new AppException(ErrorCode::UNAUTHORIZED);
        }
    }

    public function refresh($accessToken, $refreshToken)
    {
        try {
            //giai ma 2 token
            $decodeToken = JWTAuth::getJWTProvider()->decode($accessToken);
            $decodeRfToken = JWTAuth::getJWTProvider()->decode($refreshToken);

            // lay thong tin user
            $user = $this->userRepository->findById($decodeRfToken['user_id']);
            if (!$user) {
                throw new AppException(ErrorCode::USER_NON_EXISTED);
            }

            $refresh_token = $user->refresh_token;
            //lay time hien tai neu de so sanh vs exp cua token
            $now = Carbon::now()->timestamp;

            if ($now < $decodeToken['exp']) {
                JWTAuth::setToken($accessToken)->invalidate(); //vo hieu hoa access token hien tai trong truong hop no van con han
            }

            // kiem tra rf token gui len va trong db co trung nhau k
            if (($refresh_token != $refreshToken) || $now > $decodeRfToken['expires_rftoken']) {
                throw new AppException(ErrorCode::INCORRECT_RF_TOKEN);
            }

            // xu li cap lai token moi
            $token = auth()->login($user); // tao token moi
            $refreshToken = $this->createRefreshToken();

            return $this->respondWithToken($token, $refreshToken);
        } catch (TokenInvalidException $e) {
            throw new AppException(ErrorCode::TOKEN_INVALID);
        } catch (JWTException $ex) {
            throw new AppException(ErrorCode::INCORRECT_RF_TOKEN);
        }
    }

    protected function createRefreshToken()
    {
        $data = [
            'user_id' => auth()->user()->id,
            'random' => rand() . time(),
            'expires_rftoken' => time() + config('jwt.refresh_ttl')
        ];

        $refreshToken = JWTAuth::getJWTProvider()->encode($data);

        $this->userRepository->update($data['user_id'], ['refresh_token' => $refreshToken]);
        return $refreshToken;
    }

    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            //thoi gian song tinh theo giay; thay doi: config->jwt->ttl
            // 'expires_token' => config('jwt.ttl') * 60
        ]);
    }
}
