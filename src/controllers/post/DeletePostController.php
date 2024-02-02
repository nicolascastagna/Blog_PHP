<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
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
     * @return ResponseInterface
     */
    public function renderDeleteForm(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
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
     * @return ResponseInterface
     */
    public function remove(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $postRepository = $this->getPostsRepository();
            $id = PostIdChecker::getId($args);
            $success = $postRepository->deletePost($id);

            if (!$success) {
                throw new \Exception('Impossible de supprimer l\'article !');
            } else {
                return $response->withHeader('Location', "/blog")->withStatus(302);
            }
        } else {
            throw new \Exception('Une erreur est survenue');
        }
    }
}
