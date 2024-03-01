<?php

declare(strict_types=1);

namespace App\lib;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

trait FileUploadTrait
{
    /**
     * getErrorUploadMessage
     *
     * @param  int $errorCode
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
}
