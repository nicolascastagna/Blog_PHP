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

    /**
     * add
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function add(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = $request->getParsedBody();
        $error = null;

        if (!isset($formData['username']) && !isset($formData['password']) && !isset($formData['email'])) {
            $error = 'Les données du formulaire sont invalides.';
        } else {
            $username = $formData['username'];
            $password = $formData['password'];
            $email = $formData['email'];

            $userRepository = $this->getUserRepository();
            if ($userRepository->emailExists($email)) {
                $error = 'L\'adresse e-mail existe déjà.';
            } else {
                $success = $userRepository->addUser($username, $password, $email);

                if (!$success) {
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
}
