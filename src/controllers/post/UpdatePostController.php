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

class UpdatePostController
{
    /**
     * renderUpdateForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function renderUpdateForm(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $postId = PostIdChecker::getId($args);
        $post = $this->getPostsRepository()->getPost($postId);

        $userChecker = new UserChecker();
        $error = null;

        $view = new View();
        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '')
                && $userChecker->isCurrentUser($post->userId, $sessionData['id']))
            || $userChecker->isAdmin($sessionData['role'] ??  'ROLE_USER')
        ) {
            $html = $view->render('post_update.twig', ['session' => $sessionData, 'error' => $error]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * update
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();

        $postId = PostIdChecker::getId($args);
        $postRepository = $this->getPostsRepository();
        $fetchPost = $postRepository->getPost($postId);

        $error = null;
        $view = new View();

        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '')
                && $userChecker->isCurrentUser($fetchPost->userId, $sessionData['id']))
            || $userChecker->isAdmin($sessionData['role'] ??  'ROLE_USER')
        ) {
            if ($request->getMethod() === 'POST') {
                $formData = $request->getParsedBody();

                if (isset($formData['title']) === false || isset($formData['chapo']) === false || isset($formData['content']) === false) {
                    $error = 'Les données du formulaire sont invalides.';
                } else {
                    $title = $formData['title'];
                    $chapo = $formData['chapo'];
                    $content = $formData['content'];

                    $title = $title !== '' ? $title : $fetchPost->title;
                    $chapo = $chapo !== '' ? $chapo : $fetchPost->chapo;
                    $content = $content !== '' ? $content : $fetchPost->content;

                    $success = $postRepository->updatePost($postId, $title, $chapo, $content);

                    if ($success === false) {
                        $error = 'Une erreur est survenue dans la mise à jour de l\'article.';
                    } else {
                        return $response->withHeader('Location', '/blog')->withStatus(302);
                    }
                }

                $html = $view->render('post_update.twig', ['error' => $error, 'session' => $sessionData]);

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

        return $response->withStatus(403);
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
