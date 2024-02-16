<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
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
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $view = new View();
        $postId = PostIdChecker::getId($args);
        $post = $this->getPostsRepository()->getPost($postId);

        $html = $view->render('post_delete.twig', ['post' => $post, 'session' => $_SESSION]);
        $response->getBody()->write($html);

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
        if ($request->getMethod() === 'POST') {
            $postRepository = $this->getPostsRepository();
            $postId = PostIdChecker::getId($args);
            $error = null;

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

        throw new Exception('Une erreur est survenue');
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
