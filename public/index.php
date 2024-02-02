<?php

namespace App\public;

use App\controllers\Contact;
use App\controllers\Homepage;
use App\controllers\post\AddPostController;
use App\controllers\post\DeletePostController;
use App\controllers\post\IndexPostController;
use App\controllers\post\ShowPostController;
use App\controllers\post\UpdatePostController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../templates');

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', [Homepage::class, 'homepage']);
$app->get('/blog', [IndexPostController::class, 'index']);
$app->get('/contact', [Contact::class, 'contact']);
$app->get('/blog/article/{id}', [ShowPostController::class, 'show']);
$app->get('/blog/ajout-article', [AddPostController::class, 'renderCreationForm']);
$app->post('/blog/ajout-article', [AddPostController::class, 'add']);
$app->get('/blog/suppression-article/{id}', [DeletePostController::class, 'renderDeleteForm']);
$app->post('/blog/suppression-article/{id}', [DeletePostController::class, 'remove']);
$app->get('/blog/modification-article/{id}', [UpdatePostController::class, 'renderUpdateForm']);
$app->post('/blog/modification-article/{id}', [UpdatePostController::class, 'update']);


$app->get('/{routes:.+}', function(RequestInterface $request, ResponseInterface $response) use ($twig) {
    return $twig->render($response->withStatus(404), 'error.twig');
});

$app->run();
