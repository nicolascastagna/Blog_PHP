<?php

declare(strict_types=1);

namespace App\controllers\comment;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\CommentRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddCommentController
{
    /**
     * add
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function add(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $userChecker = new UserChecker();
        $formData = $request->getParsedBody();
        $postId = PostIdChecker::getId($args);

        if (!$userChecker->isAuthenticated($sessionData['token'] ?? '')) {
            $error = 'Vous n\'avez pas accès à cette page !';
            return $this->renderErrorResponse($response, $error);
        }

        if (isset($formData['content']) === false) {
            throw new Exception('Certaines informations sont manquantes.');
        }

        $content = $formData['content'];

        $commentRepository = $this->getCommentsRepository();
        $success = $commentRepository->addComment($sessionData['id'], $postId, $content);

        if ($success === false) {
            throw new Exception('Une erreur est survenue dans l\'ajout du commentaire.');
        }

        return $response->withHeader('Location', "/blog/article/{$args['id']}")->withStatus(302);
    }

    /**
     * renderErrorResponse
     *
     * @param  ResponseInterface $response
     * @param  string $error
     * @return ResponseInterface
     */
    private function renderErrorResponse(ResponseInterface $response, string $error): ResponseInterface
    {
        $view = new View();
        $html = $view->render('error.twig', ['error' => $error]);
        $response->getBody()->write($html);
        return $response;
    }

    /**
     * getCommentsRepository
     *
     * @return CommentRepository
     */
    private function getCommentsRepository(): CommentRepository
    {
        $connection = new DatabaseConnection();
        $commentRepository = new CommentRepository();
        $commentRepository->connection = $connection;

        return $commentRepository;
    }
}
