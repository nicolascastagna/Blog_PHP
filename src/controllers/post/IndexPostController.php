<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexPostController
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
     * index
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $posts = $this->getPostsRepository()->getPosts();

        $postSorter = new PostSorter();
        $sortedPosts = $postSorter->sortByRecentDate($posts);

        $view = new View();
        $html = $view->render('blogpage.twig', ['posts' => $sortedPosts]);

        $response->getBody()->write($html);

        return $response;
    }
}
