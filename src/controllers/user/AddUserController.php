<?php

namespace App\controllers\user;

use App\lib\DatabaseConnection;
use App\lib\View;
use App\model\UserRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddUserController
{
    private function getUserRepository(): UserRepository
    {
        $connection = new DatabaseConnection();
        $userRepository = new UserRepository();
        $userRepository->connection = $connection;

        return $userRepository;
    }

    /**
     * renderRegisterForm
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderRegisterForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('register.twig', []);

        $response->getBody()->write($html);

        return $response;
    }
}
