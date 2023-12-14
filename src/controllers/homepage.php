<?php

namespace App\controllers;

use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Homepage
{
    public function homepage(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('homepage.twig', ['title' => 'Homepage']);

        $response->getBody()->write($html);

        return $response;
    }
}
