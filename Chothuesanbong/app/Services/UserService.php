<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    protected UserRepository $userRepository;

    protected ImageService $imageService;

    public function __construct(UserRepository $userRepository, ImageService $imageService)
    {
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    public function createUser(array $data)
    {
        if(isset($data["avatar"]) && $data["avatar"] != null) {
            $data["avatar"] = $this->imageService->saveImageInDisk($data["avatar"],"user");
        }

        $user = $this->userRepository->create($data);
        return $user;
    }

    public function updateUser($userId, array $data)
    {
        $existingUser = $this->userRepository->findById($userId);
        if ($existingUser == null)
            throw new AppException(ErrorCode::USER_NON_EXISTED);

        //neu user trong token khac vs user truyen len = id => k sua dc
        if ($data['user_id'] != $existingUser->id)
            throw new AppException(ErrorCode::UNAUTHORIZED);

        // Update user information based on the DTO
        if (isset($data['name']) && !empty($data['name'])) {
            $existingUser->name = $data['name'];
        }

        if (isset($data['address']) && !empty($data['address'])) {
            $existingUser->address = $data['address'];
        }

        if (isset($data['phone_number']) && !empty($data['phone_number'])) {
            $existingUser->phone_number = $data['phone_number'];
        }

        if(isset($data["avatar"]) && $data["avatar"] != null) {
            $this->imageService->deleteImageInDisk($existingUser->avatar);
            $data["avatar"] = $this->imageService->saveImageInDisk($data["avatar"],"user");
        }

        // Save the updated user
        $userUpdate = $this->userRepository->update($existingUser->id, $data);
        return $userUpdate;
    }

    public function deleteUser($userId, $currentUser, $role, $accessToken)
    {
        $existingUser = $this->userRepository->findById($userId);
        if ($existingUser == null)
            throw new AppException(ErrorCode::USER_NON_EXISTED);

        //neu dung la la user hoac la admin thi dc xoa
        if (!($currentUser == $existingUser->id || $role == 1)) {
            throw new AppException(ErrorCode::UNAUTHORIZED);
        }

        // neu la user thi inactive access token // neu la admin thi de token user tu het han
        if ($role != 1) {
            JWTAuth::setToken($accessToken)->invalidate();
        }

        // inactive rf token
        $this->userRepository->update($existingUser->id, ['refresh_token' => '']);
        return $this->userRepository->delete($userId);
    }
}
