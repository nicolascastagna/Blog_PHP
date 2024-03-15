<?php

declare(strict_types=1);

namespace App\lib;

use Dotenv\Dotenv;

class MailConfig
{
    private $config = [];

    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->config = [
            'host' => getenv('MAIL_HOST'),
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD'),
            'port' => getenv('MAIL_PORT'),
            'encryption' => getenv('MAIL_ENCRYPTION'),
            'from' => getenv('MAIL_FROM_ADDRESS'),
        ];
    }

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
