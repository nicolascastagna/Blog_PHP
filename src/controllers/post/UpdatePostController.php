<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\PostIdChecker;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UpdatePostController
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
     * renderUpdateForm
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function renderUpdateForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('post_update.twig', []);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * update
     *
     * @param  RequestInterface $request
     * @param  ResponseInterface $response
     * @param  array $args
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();

            if (!isset($formData['title']) && !isset($formData['chapo']) && !isset($formData['content'])) {
                throw new Exception('Les données du formulaire sont invalides.');
            }

            $title = $formData['title'];
            $chapo = $formData['chapo'];
            $content = $formData['content'];

            $id = PostIdChecker::getId($args);
            $postRepository = $this->getPostsRepository();
            $success = $postRepository->updatePost($id, $title, $chapo, $content);

            if (!$success) {
                throw new \Exception('Impossible de modifier l\'article !');
            } else {
                return $response->withHeader('Location', "/blog")->withStatus(302);
            }
        } else {
            throw new \Exception('Une erreur est survenue');
        }
    }
}
