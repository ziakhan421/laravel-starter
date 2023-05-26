<?php

namespace App\Repositories\User;

use App\Helpers\Constants;
use App\Models\User;
use App\Traits\GeneralException;
use App\Traits\StorageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use LaravelEasyRepository\Implementations\Eloquent;

class UserRepositoryImplement extends Eloquent implements UserRepository
{
    use StorageTrait;

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

    /**
     * @throws GeneralException
     */
    public function getUserById($id)
    {
        if (Auth::user()->role !== 'admin') {
            throw new GeneralException(422, 'You are not Authorized for this.', Constants::HTTP_CODE_422);
        }
        return $this->model->find($id);
    }

    /**
     * @throws GeneralException
     */
    public function all()
    {
        if (Auth::user()->role !== 'admin') {
            throw new GeneralException(422, 'You are not Authorized for this.', Constants::HTTP_CODE_422);
        }
        return $this->model->all()->except(Auth::id());
    }

    /**
     * @throws GeneralException
     */
    public function updateProfile($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',  // just for test validation as mentioned in document
        ]);

        if ($validator->fails()) {
            throw new GeneralException(422, $validator->messages(), Constants::HTTP_CODE_422);
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $path = self::saveFileToDisk($image, 'user_profiles');
            if (!is_null($path)) {
                $user->photo = $path;
            }
        }

        $user->save();

        return $user;
    }

    /**
     * @throws GeneralException
     */
    public function update($id, $request)
    {
        if (Auth::user()->role !== 'admin' || $id === Auth::id()) {
            throw new GeneralException(422, 'You are not Authorized for this.', Constants::HTTP_CODE_422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',  // just for test validation as mentioned in document
            'is_active' => 'required|string',  // just for test validation as mentioned in document
        ]);

        if ($validator->fails()) {
            throw new GeneralException(422, $validator->messages(), Constants::HTTP_CODE_422);
        }

        $user = $this->model->find($id);
        if (!isset($user->id)) {
            throw new GeneralException(422, 'User not found.', Constants::HTTP_CODE_422);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->is_active = $request->is_active;
        if (isset($request->role) && !empty($request->role)) {
            $user->role = $request->role;
        }
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $path = self::saveFileToDisk($image, 'user_profiles');
            if (!is_null($path)) {
                $user->photo = $path;
            }
        }

        $user->save();
        return $user;
    }

    public function delete($id)
    {

        if (Auth::user()->role !== 'admin') {
            throw new GeneralException(422, 'You are not Authorized for this.', Constants::HTTP_CODE_422);
        }

        $user = $this->model->find($id);
        if (!isset($user->id)) {
            throw new GeneralException(422, 'User not found.', Constants::HTTP_CODE_422);
        }
        if (!empty($user->photo)) {
            self::deleteFileFromDisk($user->photo);
        }
        if (!empty($user->phone)) {

            //send SMS using JOB queue from different service provide providing name in .env and add relevant serviceClass in services
//            try {
//                dispatch(new JobSms($user->phone, 'Welcome user'));
//            } catch (\Throwable $th) {
//                throw $th;
//            }
        }
        if (!empty($user->email)) {

            //send Email using job queue
//            try {
//                dispatch(new JobEmail($user->email, $user));
//            } catch (\Throwable $th) {
//                throw $th;
//            }
        }

        $user->delete();
        return $user;
    }
}
