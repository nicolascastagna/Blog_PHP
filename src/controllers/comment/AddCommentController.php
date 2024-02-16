<?php

declare(strict_types=1);

namespace App\controllers\comment;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
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
        $formData = $request->getParsedBody();
        $postId = PostIdChecker::getId($args);

        if (isset($formData['content']) === false) {
            throw new Exception('Certaines informations sont manquantes.');
        }

        $content = $formData['content'];

        $user_id = 1;
        $commentRepository = $this->getCommentsRepository();
        $success = $commentRepository->addComment($user_id, $postId, $content);

        if ($success === false) {
            throw new Exception('Une erreur est survenue dans l\'ajout du commentaire.');
        }

        return $response->withHeader('Location', "/blog/article/{$args['id']}")->withStatus(302);
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
