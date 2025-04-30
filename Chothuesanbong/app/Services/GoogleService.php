<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Http\Resources\CommentResource;
use App\Repositories\BookingRepository;
use App\Repositories\CommentRepository;
use App\Repositories\FieldRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleService
{
    protected UserRepository $userRepository;
    protected AuthService $authService;

    public function __construct(UserRepository $userRepository, AuthService $authService)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

//    public function handleGoogleCallback($user)
//    {
//        $existingUser = $this->userRepository->findByGoogleId($user->id);
//        if ($existingUser) {
//            $credentials = [
//                'email' => $existingUser->email,
//                'password' => 'Google1@1'
//            ];
//        } else {
//            $data = [
//                'name' => $user->name,
//                'email' => $user->email,
//                'google_id' => $user->id,
//                'password' => 'Google1@1',
//            ];
//            $user = $this->userRepository->create($data);
//            $credentials = [
//                'email' => $user->email,
//                'password' => 'Google1@1'
//            ];
//        }
//        return $this->authService->login($credentials);
//    }


    public function handleGoogleLogin($user)
    {
        $existingUser = $this->userRepository->findByGoogleId($user->id);
        if ($existingUser) {
            $credentials = [
                'email' => $existingUser->email,
                'password' => 'Google1@1'
            ];
        } else {
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => 'Google1@1',
                'avatar' => $user->avatar,
            ];
            $user = $this->userRepository->create($data);
            $credentials = [
                'email' => $user->email,
                'password' => 'Google1@1'
            ];
        }
        return $this->authService->login($credentials);
    }

}
