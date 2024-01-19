<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexPost
{
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }

    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $posts = $this->getPostsRepository()->getPosts();

        $sortedPosts = PostSorter::sortByModificationDate($posts);

        $view = new View();
        $html = $view->render('blogpage.twig', ['posts' => $sortedPosts]);

        $response->getBody()->write($html);

        return $response;
    }
}
