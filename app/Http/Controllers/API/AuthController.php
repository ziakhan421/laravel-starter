<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;

class AuthController extends ApiController
{

    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(Request $request)
    {
        return $this->authRepository->register($request);
    }

    public function login(Request $request)
    {
        return $this->authRepository->login($request);
    }

    public function profile()
    {
        return $this->authRepository->profile();
    }

    public function logout()
    {
        return $this->authRepository->logout();
    }

    public function sendOtp(Request $request)
    {
        return $this->authRepository->sendOtp($request);
    }

    public function verifyOtp(Request $request)
    {
        return $this->authRepository->verifyOtp($request);
    }

    public function changePassword(Request $request)
    {
        return $this->authRepository->changePassword($request);
    }

    public function forgotPassword(Request $request)
    {
        return $this->authRepository->forgotPassword($request);
    }

    public function verifyForgotPassword(Request $request)
    {
        return $this->authRepository->verifyForgotPassword($request);
    }

    public function resetPassword(Request $request)
    {
        return $this->authRepository->resetPassword($request);
    }
}
