<?php

namespace App\public;

use App\controllers\Contact;
use App\controllers\Homepage;
use App\controllers\post\IndexPost;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$twig = Twig::create(
    __DIR__ . '/../templates',
    ['cache' => false]
);

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', [Homepage::class, 'homepage']);
$app->get('/blog', [IndexPost::class, 'index']);
$app->get('/contact', [Contact::class, 'contact']);

$app->get('/{routes:.+}', function (RequestInterface $request, ResponseInterface $response) use ($twig) {
    return $twig->render($response->withStatus(404), 'error.twig');
});

$app->run();
