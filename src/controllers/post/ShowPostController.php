<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\controllers\comment\AddCommentController;
use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\CommentRepository;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShowPostController
{
    /**
     * show
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function show(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $postId = PostIdChecker::getId($args);
        $error = null;

        $post = $this->getPostsRepository()->getPost($postId);
        $comment = $this->getCommentsRepository()->getComments($postId);

        $comments = array_filter($comment, fn ($comment) => $comment->status == 1);

        if ($request->getMethod() === 'POST') {
            $commentController = new AddCommentController();

            try {
                $commentController->add($request, $response, $args);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $view = new View();
        $html = $view->render('post.twig', ['post' => $post, 'comments' => $comments, 'error' => $error, 'session' => $_SESSION]);

        $response->getBody()->write($html);

        return $response;
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
