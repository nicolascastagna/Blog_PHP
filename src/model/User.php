<?php declare(strict_types=1);

namespace App\model;

class User
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $username;

    /**
     * @var string
     */
    public string $password;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $role;

    /**
     * @var string|null
     */
    public ?string $token;
}
