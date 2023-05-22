<?php

namespace App\Repositories\Auth;

use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Jobs\JobOtp;
use App\Models\User;
use App\Traits\BaseResponse;
use App\Traits\GeneralException;
use App\Traits\StorageTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use LaravelEasyRepository\Implementations\Eloquent;
use Ichtrojan\Otp\Otp;

class AuthRepositoryImplement extends Eloquent implements AuthRepository
{
    use BaseResponse, StorageTrait;

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function login($request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw new GeneralException(401, 'Unauthorized!', Constants::HTTP_CODE_401);
        }

        $user = $this->model::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            (['access_token' => $token, 'user' => $user]),
            CommonUtil::generateUUID()
        );
    }

    /**
     * @throws GeneralException
     */
    public function register($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            throw new GeneralException(422, $validator->messages(), Constants::HTTP_CODE_422);
        }

        $user = $this->model::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => 1,
            'password' => Hash::make($request->password)
        ]);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $path = self::saveFileToDisk($image, 'user_profiles');
            if (!is_null($path)) {
                $user->photo = $path;
                $user->save();
            }
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        if (!empty($user->phone)) {

            //send SMS from different service provide providing name in .env and add relevant serviceClass in services
//            try {
//                dispatch(new JobSms($user->phone, 'Welcome user'));
//            } catch (\Throwable $th) {
//                throw $th;
//            }
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            (['access_token' => $token, 'user' => $user]),
            CommonUtil::generateUUID()
        );
    }

    public function profile()
    {
        $user = Auth::user();

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            (['user' => $user]),
            CommonUtil::generateUUID()
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            'Successfully logout!!',
            CommonUtil::generateUUID()
        );
    }

    public function sendOtp($request)
    {
        $otp = new Otp;

        $otp_token = $otp->generate($request->email, 6, 5);

        $data = [
            'otp' => $otp_token->token
        ];

        // Find email and send token
        $user = $this->model::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        try {
            dispatch(new JobOtp($request->email, $data));
        } catch (\Throwable $th) {
            throw $th;
        }

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $token,
            CommonUtil::generateUUID()
        );
    }

    public function verifyOtp($request)
    {
        $otp = new Otp;

        $validate_token = $otp->validate($request->email, $request->otp);

        if (!$validate_token->status) {
            throw new GeneralException(422, $validate_token->message, Constants::HTTP_CODE_422);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $validate_token->message,
            CommonUtil::generateUUID()
        );
    }

    public function changePassword($request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            throw new GeneralException(422, $validator->messages(), Constants::HTTP_CODE_422);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            throw new GeneralException(422, 'Old password is incorrect', Constants::HTTP_CODE_422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            'Successfully change password!!',
            CommonUtil::generateUUID()
        );
    }

    public function forgotPassword($request)
    {
        $otp = new Otp;

        $otp_token = $otp->generate($request->email, 6, 5);

        $data = [
            'otp' => $otp_token->token
        ];

        try {
            dispatch(new JobOtp($request->email, $data));
        } catch (\Throwable $th) {
            throw $th;
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            'Successfully send otp!!',
            CommonUtil::generateUUID()
        );
    }

    public function verifyForgotPassword($request)
    {
        $otp = new Otp;

        $validate_token = $otp->validate($request->email, $request->otp);

        if (!$validate_token->status) {
            throw new GeneralException(422, $validate_token->message, Constants::HTTP_CODE_422);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $validate_token->message,
            CommonUtil::generateUUID()
        );
    }

    public function resetPassword($request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            throw new GeneralException(422, $validator->messages(), Constants::HTTP_CODE_422);
        }

        $user = $this->model::where('email', '=', $request->email)->first();
        if (empty($user)) {
            return self::statusResponse(
                Constants::HTTP_CODE_422,
                Constants::ERROR_MESSAGE_422,
                'Email not found!!',
                CommonUtil::generateUUID()
            );
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (\Throwable $th) {
            throw new GeneralException(422, $th, Constants::HTTP_CODE_422);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            'Successfully reset password!!',
            CommonUtil::generateUUID()
        );
    }
}
