<?php

declare(strict_types=1);

namespace App\controllers\user;

use App\lib\DatabaseConnection;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\UserRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddUserController
{
    /**
     * renderRegisterForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderRegisterForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userChecker = new UserChecker();
        if ($userChecker->isAuthenticated($sessionData['token'] ?? '')) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $view = new View();
        $html = $view->render('register.twig', []);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * add
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function add(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = $request->getParsedBody();
        $error = null;

        if (isset($formData['username']) === false && isset($formData['password']) === false && isset($formData['email']) === false) {
            $error = 'Les données du formulaire sont invalides.';
        } else {
            $username = $formData['username'];
            $password = $formData['password'];
            $email = $formData['email'];

            $userRepository = $this->getUserRepository();
            if ($userRepository->emailExists($email) === true) {
                $error = 'L\'adresse e-mail existe déjà.';
            } else {
                $success = $userRepository->addUser($username, $password, $email);

                if ($success === false) {
                    $error = 'Une erreur est survenue dans l\'enregistrement.';
                } else {
                    return $response->withHeader('Location', '/blog')->withStatus(302);
                }
            }
        }

        $view = new View();
        $html = $view->render('register.twig', ['error' => $error]);

        $response->getBody()->write($html);

        return $response;
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
