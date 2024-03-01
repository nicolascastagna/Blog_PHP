<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\Post;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use App\lib\FileUploadTrait;

class UpdatePostController
{
    use FileUploadTrait;

    /**
     * renderUpdateForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function renderUpdateForm(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $postId = PostIdChecker::getId($args);
        $post = $this->getPostsRepository()->getPost($postId);

        $userChecker = new UserChecker();
        $error = null;

        $view = new View();
        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true
                && $userChecker->isCurrentUser($post->userId, $sessionData['id']) === true)
            || $userChecker->isAdmin($sessionData['role'] ?? 'ROLE_USER')
        ) {
            $html = $view->render('post_update.twig', ['session' => $sessionData, 'error' => $error]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * update
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();
        $userChecker = new UserChecker();

        $postId = PostIdChecker::getId($args);
        $postRepository = $this->getPostsRepository();
        $fetchPost = $postRepository->getPost($postId);

        $error = null;
        if (
            ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true
                && $userChecker->isCurrentUser($fetchPost->userId, $sessionData['id']) === true)
            || $userChecker->isAdmin($sessionData['role'] ?? 'ROLE_USER')
        ) {
            return $this->handlePostRequest($request, $response, $fetchPost, $sessionData, $postRepository);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * handlePostRequest
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @param  Post $fetchPost
     * @param  array $sessionData
     * @param  PostRepository $postRepository
     *
     * @return ResponseInterface
     */
    private function handlePostRequest(RequestInterface $request, ResponseInterface $response, Post $fetchPost, array $sessionData, PostRepository $postRepository): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            $view = new View();

            if (isset($formData['title']) === false || isset($formData['chapo']) === false || isset($formData['content']) === false) {
                $error = 'Les données du formulaire sont invalides.';
            } else {
                $title = $formData['title'];
                $chapo = $formData['chapo'];
                $content = $formData['content'];

                $image = $fetchPost->image;
                $uploadedFiles = $request->getUploadedFiles();

                if (isset($uploadedFiles['file']) && $uploadedFiles['file']->getError() !== UPLOAD_ERR_NO_FILE) {
                    if ($uploadedFiles['file']->getError() !== UPLOAD_ERR_OK) {
                        $error = $this->getErrorUploadMessage($uploadedFiles['file']->getError());
                        $html = $view->render('post_update.twig', ['error' => $error, 'session' => $sessionData]);
                        $response->getBody()->write($html);

                        return $response;
                    } else {
                        try {
                            $imagePath = './assets/posts/' . $fetchPost->image;
                            if (file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            $uploadedFile = $uploadedFiles['file'];
                            $image = $this->moveUploadedFile($uploadedFile);
                        } catch (Exception $e) {
                            $error = $e->getMessage();
                            $html = $view->render('post_update.twig', ['error' => $error, 'session' => $sessionData]);
                            $response->getBody()->write($html);

                            return $response;
                        }
                    }
                } else {
                    $image = $fetchPost->image;
                }

                $title = $title !== '' ? $title : $fetchPost->title;
                $chapo = $chapo !== '' ? $chapo : $fetchPost->chapo;
                $content = $content !== '' ? $content : $fetchPost->content;

                $success = $postRepository->updatePost($fetchPost->userId, $title, $chapo, $content, $image);

                if ($success === false) {
                    $error = 'Une erreur est survenue dans la mise à jour de l\'article.';
                } else {
                    return $response->withHeader('Location', '/blog')->withStatus(302);
                }
            }

            $html = $view->render('post_update.twig', ['error' => $error, 'session' => $sessionData]);
            $response->getBody()->write($html);

            return $response;
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
}
