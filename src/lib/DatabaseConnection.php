<?php

namespace App\lib;

use Dotenv\Dotenv;
use Exception;
use PDO;

class DatabaseConnection
{
    private ?PDO $database = null;

    /**
     * getConnection
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        if ($this->database === null) {
            $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $host = getenv('DB_HOST');
            $dbname = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $password = getenv('DB_PASSWORD');

            if ($host === false || $dbname === false || $user === false || $password === false) {
                throw new Exception('Missing required database configuration.');
            }

            $this->database = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        }

        return $this->database;
    }
}
