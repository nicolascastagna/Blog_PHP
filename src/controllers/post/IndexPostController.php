<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexPostController
{
    /**
     * index
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $posts = $this->getPostsRepository()->getPosts();

        $postSorter = new PostSorter();
        $sortedPosts = $postSorter->sortByRecentDate($posts);

        $view = new View();
        $html = $view->render('blogpage.twig', ['posts' => $sortedPosts, 'session' => $sessionData]);

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
