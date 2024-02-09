<?php

namespace App\controllers;

use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Contact
{
    /**
     * contact
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function contact(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('contact.twig', ['contact' => 'Contact']);

        $response->getBody()->write($html);

        return $response;
    }
}
