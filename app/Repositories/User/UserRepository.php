<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository
{
    public function getUserById($id);

    public function all();

    public function updateProfile($request);

    public function update($id, $request);

    public function delete($id);
}
