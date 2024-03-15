<?php

declare(strict_types=1);

namespace App\controllers;

use App\lib\MailConfig;
use App\lib\SessionChecker;
use App\lib\SessionManager;
use App\lib\View;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Contact
{
    /**
     * contact
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function renderForm(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $view = new View();
        $html = $view->render('contact.twig', ['session' => $sessionData]);

        $response->getBody()->write($html);

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
    public function sendEmail(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionManager = new SessionManager();
        $sessionChecker = new SessionChecker($sessionManager);

        $sessionChecker->sessionChecker();
        $sessionData = $sessionChecker->getSessionData();

        $view = new View();

        $error = null;
        $success = null;

        if ($request->getMethod() === 'POST') {
            try {
                $mailConfig = (new MailConfig())->getConfig();

                $formData = $request->getParsedBody();

                if (!$this->checkFormData($formData)) {
                    $error = 'Tous les champs doivent être remplis.';

                    throw new Exception($error);
                }

                $firstName = $this->validateInput($formData['firstName']);
                $lastName = $this->validateInput($formData['lastName']);
                $email = $this->validateEmail($formData['email']);
                $subject = $this->validateInput($formData['subject']);
                $body = $this->validateInput($formData['body']);
                $name = $firstName . ' ' . $lastName;

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = $mailConfig['host'];
                $mail->Username = $mailConfig['username'];
                $mail->Password = $mailConfig['password'];
                $mail->SMTPSecure = $mailConfig['encryption'];
                $mail->Port = $mailConfig['port'];

                $mail->setFrom($mailConfig['from'], $name);
                $mail->addAddress($mailConfig['username']);

                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                $mail->isHTML(true);
                $htmlBody = $view->render('email_template.twig', ['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'subject' => $subject, 'body' => $body]);

                $mail->Subject = $subject;
                $mail->Body = $htmlBody;
                $mail->AltBody = strip_tags($htmlBody);

                $mail->send();

                $success = 'Votre message a bien été envoyé.';

                $html = $view->render('contact.twig', ['session' => $sessionData, 'error' => $error, 'success' => $success]);
                $response->getBody()->write($html);
            } catch (Exception $e) {
                $error = 'Erreur lors de l\'envoi de l\'e-mail: ' . $e->getMessage();

                return $this->renderErrorResponse($response, $error);
            }
        }

        return $response;
    }

    /**
     * validateInput
     *
     * @param  string $input
     *
     * @return string
     */
    private function validateInput(string $input): string
    {
        return (is_string($input)) ? strip_tags($input) : null;
    }

    /**
     * validateEmail
     *
     * @param  string $email
     *
     * @return string
     */
    private function validateEmail(string $email): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return strip_tags($email);
        }

        return null;
    }

    /**
     * checkFormData
     *
     * @param  array $formData
     *
     * @return bool
     */
    private function checkFormData(array $formData): bool
    {
        foreach ($formData as $field) {
            if (empty($field)) {
                return false;
            }
        }

        return true;
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

        return $response->withStatus(400);
    }
}
