<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findUserById($id)
    {
        $result = $this->userRepository->getUserById($id);

        return $this->successResponse($result);
    }

    public function getAllUsers()
    {
        $result = $this->userRepository->all();
        return $this->successResponse($result);
    }

    public function updateProfile(Request $request)
    {
        $result = $this->userRepository->updateProfile($request);
        return $this->successResponse($result, 'Profile updated Successfully');
    }

    public function updateUser($id, Request $request)
    {
        $result = $this->userRepository->update($id, $request);
        return $this->successResponse($result, 'User updated Successfully');
    }

    public function delete($id)
    {
        $result = $this->userRepository->delete($id);
        return $this->successResponse($result, 'User deleted Successfully');
    }
}
