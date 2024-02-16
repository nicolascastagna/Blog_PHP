<?php

declare(strict_types=1);

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\SessionChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UpdatePostController
{
    /**
     * renderUpdateForm
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderUpdateForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionChecker = new SessionChecker();
        $sessionChecker->sessionChecker();
        $view = new View();
        $html = $view->render('post_update.twig', ['session' => $_SESSION]);

        $response->getBody()->write($html);

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
        if ('POST' === $request->getMethod()) {
            $formData = $request->getParsedBody();
            $error = null;

            if (!isset($formData['title']) && !isset($formData['chapo']) && !isset($formData['content'])) {
                $error = 'Les données du formulaire sont invalides.';
            } else {
                $title = $formData['title'];
                $chapo = $formData['chapo'];
                $content = $formData['content'];

                $id = PostIdChecker::getId($args);
                $postRepository = $this->getPostsRepository();
                $success = $postRepository->updatePost($id, $title, $chapo, $content);

                if (!$success) {
                    $error = 'Une erreur est survenue dans la mise à jour de l\'article.';
                } else {
                    return $response->withHeader('Location', '/blog')->withStatus(302);
                }
            }

            $view = new View();
            $html = $view->render('post_update.twig', ['error' => $error]);

            $response->getBody()->write($html);

            return $response;
        }

        throw new Exception('Une erreur est survenue');
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
