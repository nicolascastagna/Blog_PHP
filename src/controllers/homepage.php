<?php

namespace App\controllers;

use App\controllers\post\IndexPost;
use App\lib\PostSorter;
use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Homepage
{
    public function homepage(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $posts = IndexPost::posts;
        $sortedPosts = PostSorter::sortByModificationDate($posts);

        $lastPosts = array_slice($sortedPosts, 0, 3);
        $view = new View();
        $html = $view->render('homepage.twig', ['posts' => $lastPosts]);

        $response->getBody()->write($html);

        return $response;
    }
}
