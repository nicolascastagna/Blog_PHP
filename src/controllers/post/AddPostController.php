<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddPostController
{
    /**
     * renderCreationForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderCreationForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $view = new View();
        $html = $view->render('post_add.twig', ['session' => $_SESSION]);

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

        if (!isset($formData['title']) || !isset($formData['chapo']) || !isset($formData['content'])) {
            $error = 'Les données du formulaire sont invalides.';
        } else {
            $title = $formData['title'];
            $chapo = $formData['chapo'];
            $content = $formData['content'];

            $user_id = 1;
            $postRepository = $this->getPostsRepository();
            $success = $postRepository->addPost($user_id, $title, $content, $chapo);

            if (!$success) {
                $error = 'Une erreur est survenue dans l\'ajout de l\'article.';
            } else {
                return $response->withHeader('Location', '/blog')->withStatus(302);
            }
        }

        $view = new View();
        $html = $view->render('post_add.twig', ['error' => $error]);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * getPostsRepository
     *
     * @return PostRepository
     */
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }
}
