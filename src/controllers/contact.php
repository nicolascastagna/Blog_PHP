<?php

declare(strict_types=1);

namespace App\controllers;

use App\lib\SessionChecker;
use App\lib\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Contact
{
    /**
     * contact
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function contact(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $view = new View();
        $html = $view->render('contact.twig', ['session' => $_SESSION]);

        $response->getBody()->write($html);

        return $response;
    }
}
