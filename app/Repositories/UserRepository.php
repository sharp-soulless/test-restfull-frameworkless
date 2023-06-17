<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByUsername(string $username): User
    {
        $user = new User();
        $user->username = $username;

        return $user;
    }
}