<?php

namespace App\lib;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private $loader;
    private $twig;

    public function __construct()
    {
        $this->loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($this->loader, [
            'cache' => false,
            'debug' => true
        ]);
    }

    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}
