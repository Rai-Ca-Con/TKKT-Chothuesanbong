<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\CreateUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Responses\APIResponse;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

//    public function index()
//    {
//        //
//        return json_encode($this->userService->getAllUsers());
//    }


    public function store(CreateUserRequest $createUserRequest)
    {
        $data = $createUserRequest->validated();
        $user = $this->userService->createUser($data);
        return APIResponse::success($user);
    }

    public function update(UpdateUserRequest $updateUserRequest, string $id)
    {
        $data = $updateUserRequest->validated();
        $data["user_id"] = auth()->user()->id;
        $userUpdate = $this->userService->updateUser($id, $data);
        return APIResponse::success($userUpdate);
    }

    public function destroy(string $id)
    {
        $userCurrent = auth()->user()->id;
        $role = auth()->user()->is_admin;
        $accessToken = request()->bearerToken();

        $userId = $this->userService->deleteUser($id, $userCurrent, $role,$accessToken);
        return APIResponse::success($userId);
    }
}
