<?php

namespace App\controllers\post;

use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexPost
{
    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('blogpage.twig', ['title' => 'page de blog']);

        $response->getBody()->write($html);

        return $response;
    }
}
