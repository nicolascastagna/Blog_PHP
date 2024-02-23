<?php

declare(strict_types=1);

namespace App\lib;

use App\model\UserRepository;

class SessionChecker
{
    private SessionManager $sessionManager;

    /**
     * __construct
     *
     * @param  SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * sessionChecker
     *
     */
    public function sessionChecker(): void
    {
        $this->sessionManager->startSession();

        if ($user = $this->sessionManager->get('user')) {
            $userRepository = $this->getUserRepository();
            $lastRefreshTime = $user['last_refresh'] ?? 0;

            if ((time() - $lastRefreshTime) >= 600) {
                $newToken = bin2hex(random_bytes(16));

                $this->sessionManager->set('user', [
                    ...$user,
                    'token' => $newToken,
                    'last_refresh' => time(),
                ]);

                $userId = $user['id'];
                if ($userId !== null) {
                    $userRepository->setToken($newToken, $userId);
                } else {
                    $this->sessionManager->destroy();
                }
            }
        }
    }

    /**
     * getSessionData
     *
     * @return array
     */
    public function getSessionData(): array
    {
        return $this->sessionManager->get('user') ?? [];
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
