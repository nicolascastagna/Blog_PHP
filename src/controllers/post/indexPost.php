<?php

namespace App\controllers\post;

use App\lib\PostSorter;
use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexPost
{
    const posts = [
        [
            'id' => 1,
            'author' => 'Auteur 1',
            'title' => 'Premier faux titre',
            'creation_date' => '03/03/2023 à 15h30min12s',
            'chapo' => "Introduction au premier faux blog post.",
        ],
        [
            'id' => 2,
            'author' => 'Auteur 2',
            'title' => 'Deuxième faux titre',
            'creation_date' => '04/03/2023 à 10h45min20s',
            'chapo' => "Suite du deuxième faux blog post.",
        ],
        [
            'id' => 3,
            'author' => 'Auteur 3',
            'title' => 'Troisième faux titre',
            'creation_date' => '05/03/2023 à 08h20min05s',
            'chapo' => "Conclusion du troisième faux blog post.",
        ],
    ];
    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $posts = self::posts;
        $sortedPosts = PostSorter::sortByModificationDate($posts);

        $view = new View();
        $html = $view->render('blogpage.twig', ['posts' => $sortedPosts]);

        $response->getBody()->write($html);

        return $response;
    }
}
