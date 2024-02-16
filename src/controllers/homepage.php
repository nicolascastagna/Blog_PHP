<?php

declare(strict_types=1);

namespace App\controllers;

use App\lib\DatabaseConnection;
use App\lib\PostSorter;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\PostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Homepage
{
    /**
     * homepage
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function homepage(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();

        $posts = $this->getPostsRepository()->getPosts();
        $postSorter = new PostSorter();
        $sortedPosts = $postSorter->sortByRecentDate($posts);

        $lastPosts = array_slice($sortedPosts, 0, 3);
        $view = new View();
        $html = $view->render('homepage.twig', ['posts' => $lastPosts, 'session' => $_SESSION]);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * getPostsRepository.
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
