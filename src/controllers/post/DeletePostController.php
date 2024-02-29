<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DeletePostController
{
    /**
     * renderDeleteForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function renderDeleteForm(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();
        $error = null;

        $view = new View();
        $postId = PostIdChecker::getId($args);
        $post = $this->getPostsRepository()->getPost($postId);

        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '')
                && $userChecker->isCurrentUser($post->userId, $sessionData['id']))
            || $userChecker->isAdmin($sessionData['role'] ??  'ROLE_USER')
        ) {
            $html = $view->render('post_delete.twig', ['post' => $post, 'session' => $sessionData, 'error' => $error]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * remove
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function remove(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();

        $postRepository = $this->getPostsRepository();
        $postId = PostIdChecker::getId($args);
        $fetchPost = $postRepository->getPost($postId);

        $error = null;
        $view = new View();
        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '')
                && $userChecker->isCurrentUser($fetchPost->userId, $sessionData['id']))
            || $userChecker->isAdmin($sessionData['role'] ??  'ROLE_USER')
        ) {
            if ($request->getMethod() === 'POST') {
                $success = $postRepository->deletePost($postId);

                if ($success === false) {
                    $error = 'Une erreur est survenue dans la suppression de l\'article.';
                } else {
                    return $response->withHeader('Location', '/blog')->withStatus(302);
                }

                $view = new View();
                $html = $view->render('post_delete.twig', ['error' => $error]);

                $response->getBody()->write($html);

                return $response;
            }
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * renderErrorResponse
     *
     * @param  ResponseInterface $response
     * @param  string $error
     *
     * @return ResponseInterface
     */
    private function renderErrorResponse(ResponseInterface $response, string $error): ResponseInterface
    {
        $view = new View();
        $html = $view->render('error.twig', ['error' => $error]);
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
