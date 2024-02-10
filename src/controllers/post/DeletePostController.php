<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DeletePostController
{
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

    /**
     * renderDeleteForm
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @param  array $args
     *
     * @return ResponseInterface
     */
    public function renderDeleteForm(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = new View();
        $id = PostIdChecker::getId($args);
        $post = $this->getPostsRepository()->getPost($id);

        $html = $view->render('post_delete.twig', ['post' => $post]);
        $response->getBody()->write($html);

        return $response;
    }

    /**
     * remove
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function remove(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $postRepository = $this->getPostsRepository();
            $id = PostIdChecker::getId($args);
            $error = null; // Initialisation de la variable d'erreur

            $success = $postRepository->deletePost($id);

            if (!$success) {
                $error = 'Une erreur est survenue dans la suppression de l\'article.';
            } else {
                return $response->withHeader('Location', '/blog')->withStatus(302);
            }

            $view = new View();
            $html = $view->render('post_delete.twig', ['error' => $error]);

            $response->getBody()->write($html);

            return $response;
        } else {
            throw new \Exception('Une erreur est survenue');
        }
    }
}
