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
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            if (empty($host) || empty($dbname) || empty($user) || empty($password)) {
                throw new Exception('Missing required database configuration.');
            }

            $this->database = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        }

        return $this->database;
    }
}
