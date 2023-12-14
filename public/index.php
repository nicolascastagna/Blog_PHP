<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\lib\View;

$view = new View();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page = ltrim($path, '/');

$page = empty($page) ? 'accueil' : $page;

switch ($page) {
    case 'accueil':
        echo $view->render('homepage.twig', ['title' => 'Homepage']);
        break;
    case 'post':
        echo $view->render('post.twig', ['title' => 'page d\'article']);
        break;
    case 'contact':
        echo $view->render('contact.twig', ['test' => 'contact']);
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo $view->render('error.twig');
        break;
}
