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
use Nyholm\Psr7\UploadedFile;
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

                if (isset($uploadedFiles['file']) === true && $uploadedFiles['file']->getError() === UPLOAD_ERR_OK) {
                    if (!$this->processImage($request, $image, $error)) {
                        $html = $view->render('post_add.twig', ['error' => $error, 'session' => $sessionData]);
                        $response->getBody()->write($html);
                        return $response;
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
     * processImage
     *
     * @param  RequestInterface $request
     * @param  string|null $image
     * @param  string|null $error
     * @return bool
     */
    private function processImage(RequestInterface $request, ?string &$image, ?string &$error): bool
    {
        $uploadedFiles = $request->getUploadedFiles();

        if (isset($uploadedFiles['file'])) {
            $uploadedFile = $uploadedFiles['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $image = $this->moveUploadedFile($uploadedFile);
                return true;
            } else {
                switch ($uploadedFile->getError()) {
                    case UPLOAD_ERR_INI_SIZE:
                        $error = 'La taille du fichier dépasse la limite autorisée.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $error = 'La taille du fichier téléchargé dépasse la limite définie dans le formulaire.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error = 'Le fichier n\'a été que partiellement téléchargé.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error = 'Aucun fichier n\'a été téléchargé.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error = 'Le dossier temporaire est manquant.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error = 'Échec de l\'écriture du fichier sur le disque.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error = 'Une extension PHP a arrêté le téléchargement du fichier.';
                        break;
                    default:
                        $error = 'Une erreur inconnue est survenue lors du téléchargement du fichier.';
                }
                return false;
            }
        } else {
            $error = 'Aucun fichier n\'a été envoyé.';
            return true;
        }
    }

    /**
     * moveUploadedFile
     *
     * @param  UploadedFileInterface $uploadedFile
     * @return string
     */
    private function moveUploadedFile(UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $allowedExtensions = ['png', 'jpg', 'jpeg'];

        if (!in_array(strtolower($extension), $allowedExtensions)) {
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
