<?php

declare(strict_types=1);

namespace App\controllers\user;

use App\lib\DatabaseConnection;
use App\model\UserRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogoutUserController
{
    /**
     * logout
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function logout(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['user']['id']) === true ? $_SESSION['user']['id'] : null;

        session_destroy();

        if ($userId !== null) {
            $userRepository = $this->getUserRepository();
            $userRepository->setToken(null, $userId);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
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
