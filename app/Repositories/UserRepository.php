<?php

declare(strict_types=1);


namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{

    public function getAllUser(): Collection
    {
        return User::all();
    }

    public function getUserById($userId)
    {
        return User::find($userId);
    }

    public function deleteUser($userId): int
    {
        return User::destroy($userId);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userId, array $newDetails)
    {
        return User::whereId($userId)->update($newDetails);
    }
}
