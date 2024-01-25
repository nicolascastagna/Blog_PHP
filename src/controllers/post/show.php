<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Show
{
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }

    public function show(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        if (isset($args['id']) && $args['id'] > 0) {
            $id = $args['id'];
        } else {
            throw new Exception('Aucun post trouvé');
        }

        $post = $this->getPostsRepository()->getPost($id);

        $view = new View();
        $html = $view->render('post.twig', ['post' => $post]);

        $response->getBody()->write($html);

        return $response;
    }
}
