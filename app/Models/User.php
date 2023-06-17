<?php

namespace App\Models;

class User
{
    public $id;
    public $username;
    public $password;

    public function __construct()
    {
        // stub data
        $this->id = rand(1, 100);
        $this->username = str_shuffle(implode(range('a', 'z')));
        $this->password = password_hash('password', PASSWORD_DEFAULT);
    }
}