<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShowPostController
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
        $id = PostIdChecker::getId($args);

        $post = $this->getPostsRepository()->getPost($id);

        $view = new View();
        $html = $view->render('post.twig', ['post' => $post]);

        $response->getBody()->write($html);

        return $response;
    }
}
