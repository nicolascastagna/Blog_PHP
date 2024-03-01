<?php

declare(strict_types=1);

namespace App\controllers\comment;

use App\lib\DatabaseConnection;
use App\lib\SessionChecker;
use App\lib\SessionManager;
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

        $comments = $this->getCommentsRepository()->getWaitingComments();

        $view = new View();
        $html = $view->render('admin.twig', ['comments' => $comments, 'session' => $sessionData]);

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
