<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\Lib\UserChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;

class AddPostController
{
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
                $title = $formData['title'];
                $chapo = $formData['chapo'];
                $content = $formData['content'];

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
                $success = $postRepository->addPost($sessionData['id'], $title, $content, $chapo, $image);

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
     * getErrorUploadMessage
     * 
     * @param int $errorCode
     * 
     * @return string
     */
    private function getErrorUploadMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'La taille du fichier dépasse la limite autorisée.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'La taille du fichier téléchargé dépasse la limite définie dans le formulaire.';
            case UPLOAD_ERR_PARTIAL:
                return 'Le fichier n\'a été que partiellement téléchargé.';
            case UPLOAD_ERR_NO_FILE:
                return 'Aucun fichier n\'a été téléchargé.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Le dossier temporaire est manquant.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Échec de l\'écriture du fichier sur le disque.';
            case UPLOAD_ERR_EXTENSION:
                return 'Une extension PHP a arrêté le téléchargement du fichier.';
            default:
                return 'Une erreur inconnue est survenue lors du téléchargement du fichier.';
        }
    }

    /**
     * moveUploadedFile
     *
     * @param  UploadedFileInterface $uploadedFile
     *
     * @return string
     */
    private function moveUploadedFile(UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $allowedExtensions = ['png', 'jpg', 'jpeg'];

        if (!in_array(strtolower($extension), $allowedExtensions, true)) {
            throw new InvalidArgumentException('Le format de l\'image n\'est pas pris en charge.');
        }

        $directory = './assets/posts';
        $filename = uniqid() . '.' . $extension;

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
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
