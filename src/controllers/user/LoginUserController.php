<?php

declare(strict_types=1);

namespace App\controllers\user;

use App\lib\DatabaseConnection;
use App\lib\View;
use App\model\UserRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoginUserController
{
    /**
     * renderLoginForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderLoginForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('login.twig', []);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * login
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function login(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = $request->getParsedBody();
        $error = null;

        if (!isset($formData['email']) && !isset($formData['password'])) {
            $error = 'Les données du formulaire sont invalides.';
        } else {
            $email = $formData['email'];
            $password = $formData['password'];

            $userRepository = $this->getUserRepository();
            $user = $userRepository->login($email, $password);

            if ($user === null) {
                $error = 'Identifiants invalides.';
            } else {
                return $response->withHeader('Location', '/')->withStatus(302);
            }
        }

        $view = new View();
        $html = $view->render('login.twig', ['error' => $error]);

        $response->getBody()->write($html);

        return $response;
    }


    private function getUserRepository(): UserRepository
    {
        $connection = new DatabaseConnection();
        $userRepository = new UserRepository();
        $userRepository->connection = $connection;

        return $userRepository;
    }
}
