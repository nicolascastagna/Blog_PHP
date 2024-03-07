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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ManageCommentsController
{
    /**
     * renderComments
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderComments(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();

        $comments = $this->getCommentsRepository()->getWaitingComments();

        $view = new View();
        if (
            $userChecker->isAuthenticated($sessionData['token'] ?? '') === true
            && $userChecker->isAdmin($sessionData['role'] ?? '')
        ) {
            $html = $view->render('admin.twig', ['comments' => $comments, 'session' => $sessionData]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * validateComments
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function validateComments(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();

        $commentId = PostIdChecker::getId($args);
        $commentRepository = $this->getCommentsRepository();
        $fetchComment = $commentRepository->getComment($commentId);

        $error = null;
        $view = new View();
        if (
            $userChecker->isAuthenticated($sessionData['token'] ?? '') === true
            && $userChecker->isAdmin($sessionData['role'] ?? '')
        ) {
            if ($request->getMethod() === 'POST') {
                $success = $commentRepository->updateCommentStatus($fetchComment->commentId);

                if ($success === false) {
                    $error = 'Une erreur est survenue dans la validation du commentaire';
                } else {
                    return $response->withHeader('Location', '/admin')->withStatus(302);
                }
            }
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        $html = $view->render('admin.twig', ['error' => $error, 'session' => $sessionData]);
        $response->getBody()->write($html);

        return $response;
    }

    /**
     * renderErrorResponse
     *
     * @param  ResponseInterface $response
     * @param  string $error
     *
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
