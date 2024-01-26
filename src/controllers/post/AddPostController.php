<?php

namespace App\controllers\post;

use App\lib\DatabaseConnection;
use App\lib\View;
use App\model\PostRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddPostController
{
    private function getPostsRepository(): PostRepository
    {
        $connection = new DatabaseConnection();
        $postRepository = new PostRepository();
        $postRepository->connection = $connection;

        return $postRepository;
    }

    public function renderCreationForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();
        $html = $view->render('post_add.twig', []);

        $response->getBody()->write($html);

        return $response;
    }

    public function add(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = $request->getParsedBody();

        if (!empty($formData['title']) && !empty($formData['chapo']) && !empty($formData['content'])) {
            $title = $formData['title'];
            $chapo = $formData['chapo'];
            $content = $formData['content'];
        } else {
            throw new Exception('Les données du formulaire sont invalides.');
        }
        $user_id = 1;
        $postRepository = $this->getPostsRepository();
        $success =  $postRepository->addPost($user_id, $title, $content, $chapo);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter l\'article !');
        } else {
            return $response->withHeader('Location', "/blog")->withStatus(302);
        }
    }
}
