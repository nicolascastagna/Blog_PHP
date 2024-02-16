<?php

declare(strict_types=1);

namespace App\lib;

use App\model\UserRepository;

class SessionChecker
{
    /**
     * sessionChecker
     */
    public function sessionChecker(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $userRepository = $this->getUserRepository();
            $lastRefreshTime = $_SESSION['user']['last_refresh'] ?? 0;

            if (time() - $lastRefreshTime >= 1800) {
                $newToken = bin2hex(random_bytes(16));

                $_SESSION['user']['token'] = $newToken;
                $_SESSION['user']['last_refresh'] = time();

                $userId = $_SESSION['user']['id'] ?? null;
                if ($userId !== null) {
                    $userRepository->setToken($userId, $newToken);
                } else {
                    session_unset();
                    session_destroy();
                }
            }
        }
    }

    /**
     * getUserRepository
     *
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        $connection = new DatabaseConnection();
        $userRepository = new UserRepository();
        $userRepository->connection = $connection;

        return $userRepository;
    }
}
