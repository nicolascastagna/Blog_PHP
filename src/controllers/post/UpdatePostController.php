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
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }

    public function renderUpdateForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('post_update.twig', []);

        $response->getBody()->write($html);

        return $response;
    }

    public function update(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();

            if (!empty($formData['title']) && !empty($formData['chapo']) && !empty($formData['content'])) {
                $title = $formData['title'];
                $chapo = $formData['chapo'];
                $content = $formData['content'];
            } else {
                throw new Exception('Les données du formulaire sont invalides.');
            }
            $id = PostIdChecker::getId($args);
            $postRepository = $this->getPostsRepository();
            $success =  $postRepository->updatePost($id, $title, $chapo, $content);

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
