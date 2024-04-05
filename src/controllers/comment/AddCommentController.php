<?php

declare(strict_types=1);

namespace App\controllers\comment;

use App\lib\CheckerId;
use App\lib\DatabaseConnection;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\CommentRepository;
use App\model\PostRepository;
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

        $postId = CheckerId::getId($args);
        $post = $this->getPostsRepository()->getPost($postId);

        $view = new View();
        $successMessage = null;

        if ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true) {
            if (isset($formData['content']) === false) {
                throw new Exception('Certaines informations sont manquantes.');
            }

            $content = htmlspecialchars($formData['content']);

            $commentRepository = $this->getCommentsRepository();
            $comment = $commentRepository->getComments($postId);
            $comments = array_filter($comment, fn ($comment) => $comment->status == 1);
            $success = $commentRepository->addComment($sessionData['id'], $postId, $content);

            if ($success === false) {
                throw new Exception('Une erreur est survenue dans l\'ajout du commentaire.');
            } else {
                $successMessage = 'Votre commentaire a bien été envoyé et en attente de validation';
            }
            $html = $view->render('post.twig', ['post' => $post, 'comments' => $comments, 'session' => $sessionData, 'success' => $successMessage]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

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

        return $response->withStatus(403);
    }

    /**
     * getPostsRepository
     *
     * @return PostRepository
     */
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
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
