<?php

namespace App\controllers\comment;

use App\lib\DatabaseConnection;
use App\model\CommentRepository;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddCommentController
{
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

    /**
     * add
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function add(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $formData = $request->getParsedBody();

        if (!isset($formData['content'])) {
            throw new Exception('Les données du formulaire sont invalides.');
        }

        $content = $formData['content'];

        $user_id = 1;
        $commentRepository = $this->getCommentsRepository();
        $success = $commentRepository->addComment($user_id, $args['id'], $content);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter le commentaire !');
        } else {
            return $response->withHeader('Location', "/blog/article/{$args['id']}")->withStatus(302);
        }
    }
}
