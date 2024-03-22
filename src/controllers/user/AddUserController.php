<?php

declare(strict_types=1);

namespace App\controllers\user;

use App\lib\DatabaseConnection;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\UserRepository;
use PDOException;
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
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $userChecker = new UserChecker();
        if ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true) {
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
            $username = htmlspecialchars($formData['username']);
            $password = htmlspecialchars($formData['password']);
            $email = htmlspecialchars($formData['email']);

            $userRepository = $this->getUserRepository();

            try {
                $userRepository->addUser($username, $password, $email);
                return $response->withHeader('Location', '/connexion')->withStatus(302);
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $error = 'Cette adresse mail existe déjà.';
                } else {
                    $error = 'Une erreur est survenue dans l\'enregistrement.';
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
