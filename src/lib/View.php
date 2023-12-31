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
        try {
            return $this->twig->render($template, $data);
        } catch (\Twig\Error\LoaderError $e) {
            return 'Erreur de chargement du template: ' . $e->getMessage();
        } catch (\Twig\Error\RuntimeError $e) {
            return 'Erreur d\'exécution du template: ' . $e->getMessage();
        } catch (\Twig\Error\SyntaxError $e) {
            return 'Erreur de syntaxe du template: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'Une erreur s\'est produite: ' . $e->getMessage();
        }
    }
}
