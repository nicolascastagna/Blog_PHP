<?php

namespace App\model;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public string $role;
    public ?string $token;
}
