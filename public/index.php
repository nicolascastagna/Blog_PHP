<?php

namespace App;

use App\controllers\Homepage;
use App\controllers\Contact;
use App\controllers\post\IndexPostController;
use App\controllers\post\ShowPostController;
use App\controllers\post\AddPostController;
use App\controllers\post\DeletePostController;
use App\controllers\post\UpdatePostController;
use App\controllers\user\AddUserController;
use App\controllers\user\LoginUserController;
use App\controllers\user\LogoutUserController;
use App\controllers\comment\AddCommentController;
use App\controllers\comment\ManageCommentsController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../templates');

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', [Homepage::class, 'homepage']);
$app->get('/blog', [IndexPostController::class, 'index']);
$app->get('/contact', [Contact::class, 'renderForm']);
$app->get('/blog/article/{id}', [ShowPostController::class, 'show']);
$app->get('/blog/ajout-article', [AddPostController::class, 'renderCreationForm']);
$app->get('/blog/suppression-article/{id}', [DeletePostController::class, 'renderDeleteForm']);
$app->get('/blog/modification-article/{id}', [UpdatePostController::class, 'renderUpdateForm']);
$app->get('/inscription', [AddUserController::class, 'renderRegisterForm']);
$app->get('/connexion', [LoginUserController::class, 'renderLoginForm']);
$app->get('/deconnexion', [LogoutUserController::class, 'logout']);
$app->get('/admin', [ManageCommentsController::class, 'renderComments']);

$app->post('/contact', [Contact::class, 'sendEmail']);
$app->post('/blog/ajout-article', [AddPostController::class, 'add']);
$app->post('/blog/suppression-article/{id}', [DeletePostController::class, 'remove']);
$app->post('/blog/modification-article/{id}', [UpdatePostController::class, 'update']);
$app->post('/blog/article/{id}/ajout-commentaire', [AddCommentController::class, 'add']);
$app->post('/inscription', [AddUserController::class, 'add']);
$app->post('/connexion', [LoginUserController::class, 'login']);
$app->post('/admin/valider-commentaire/{id}', [ManageCommentsController::class, 'validateComments']);
$app->post('/admin/suppression-commentaire/{id}', [ManageCommentsController::class, 'invalidateComments']);

$app->get('/{routes:.+}', function (RequestInterface $request, ResponseInterface $response) use ($twig) {
    return $twig->render($response->withStatus(404), 'error.twig');
});

$app->run();
