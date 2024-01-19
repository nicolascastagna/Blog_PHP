<?php

namespace App\controllers;

use App\controllers\post\IndexPost;
use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Homepage
{
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }

    public function homepage(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $posts = $this->getPostsRepository()->getPosts();
        $sortedPosts = PostSorter::sortByModificationDate($posts);

        $lastPosts = array_slice($sortedPosts, 0, 3);
        $view = new View();
        $html = $view->render('homepage.twig', ['posts' => $lastPosts]);

        $response->getBody()->write($html);

        return $response;
    }
}
