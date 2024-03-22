<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\FileUploadTrait;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddPostController
{
    use FileUploadTrait;

    /**
     * renderCreationForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderCreationForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $userChecker = new UserChecker();
        $error = null;

        $view = new View();

        if ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true) {
            $html = $view->render('post_add.twig', ['session' => $sessionData, 'error' => $error]);
            $response->getBody()->write($html);
        } else {
            $error = 'Vous n\'avez pas accès à cette page !';

            return $this->renderErrorResponse($response, $error);
        }

        return $response;
    }

    /**
     * add
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function add(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $userChecker = new UserChecker();
        $error = null;
        $view = new View();

        if ($userChecker->isAuthenticated($sessionData['token'] ?? '') === true) {
            $formData = $request->getParsedBody();

            if (isset($formData['title']) === false && isset($formData['chapo']) === false && isset($formData['content']) === false) {
                $error = 'Les données du formulaire sont invalides.';
            } else {
                $title = htmlspecialchars($formData['title']);
                $chapo = htmlspecialchars($formData['chapo']);
                $content = htmlspecialchars($formData['content']);

                $image = null;
                $uploadedFiles = $request->getUploadedFiles();

                if (isset($uploadedFiles['file']) && $uploadedFiles['file']->getError() !== UPLOAD_ERR_NO_FILE) {
                    if ($uploadedFiles['file']->getError() !== UPLOAD_ERR_OK) {
                        $error = $this->getErrorUploadMessage($uploadedFiles['file']->getError());
                        $html = $view->render('post_add.twig', ['error' => $error, 'session' => $sessionData]);
                        $response->getBody()->write($html);

                        return $response;
                    } else {
                        try {
                            $uploadedFile = $uploadedFiles['file'];
                            $image = $this->moveUploadedFile($uploadedFile);
                        } catch (Exception $e) {
                            $error = $e->getMessage();
                            $html = $view->render('post_add.twig', ['error' => $error, 'session' => $sessionData]);
                            $response->getBody()->write($html);

                            return $response;
                        }
                    }
                }

                $postRepository = $this->getPostsRepository();
                $success = $postRepository->addPost($sessionData['id'], $title, $chapo, $content, $image);

                if ($success === false) {
                    $error = 'Une erreur est survenue dans l\'ajout de l\'article.';
                } else {
                    return $response->withHeader('Location', '/blog')->withStatus(302);
                }
            }
            $html = $view->render('post_add.twig', ['error' => $error, 'session' => $sessionData]);

            $response->getBody()->write($html);

            return $response;
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
}
