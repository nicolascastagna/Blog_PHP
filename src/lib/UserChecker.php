<?php

declare(strict_types=1);

namespace App\Lib;

use App\lib\DatabaseConnection;
use App\model\UserRepository;

class UserChecker
{
    /**
     * isAuthenticated
     *
     * @param  string $token
     *
     * @return bool
     */
    public function isAuthenticated(string $token): bool
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        if ($userRepository->checkToken($token)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * isCurrentUser
     *
     * @param  int $userId
     * @param  int $current_user_id
     *
     * @return bool
     */
    public function isCurrentUser(int $userId, int $current_user_id): bool
    {
        if ($userId === $current_user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * isAdmin
     *
     * @param  string $user_role
     *
     * @return bool
     */
    public function isAdmin(string $user_role): bool
    {
        if ($user_role === 'ROLE_ADMIN') {
            return true;
        } else {
            return false;
        }
    }
}
