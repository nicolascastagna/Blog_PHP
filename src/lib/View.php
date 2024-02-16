<?php

declare(strict_types=1);

namespace App\lib;

use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private $loader;

    private $twig;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($this->loader, [
            'cache' => false,
            'debug' => true,
        ]);
    }

    /**
     * render
     *
     * Render the template with the provided data
     *
     * @param string $template The template file path
     * @param array  $data     An associative array of data to pass to the template
     *
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        try {
            return $this->twig->render($template, $data);
        } catch (\Twig\Error\LoaderError $exception) {
            return 'Erreur de chargement du template: ' . $exception->getMessage();
        } catch (\Twig\Error\RuntimeError $exception) {
            return 'Erreur d\'exécution du template: ' . $exception->getMessage();
        } catch (\Twig\Error\SyntaxError $exception) {
            return 'Erreur de syntaxe du template: ' . $exception->getMessage();
        } catch (Exception $exception) {
            return 'Une erreur s\'est produite: ' . $exception->getMessage();
        }
    }
}
