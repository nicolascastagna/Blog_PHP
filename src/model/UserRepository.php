<?php

declare(strict_types=1);

namespace App\model;

use App\lib\DatabaseConnection;

class UserRepository
{
    /**
     * @var DatabaseConnection
     */
    public DatabaseConnection $connection;

    /**
     * getUsers
     *
     * @return array
     */
    public function getUsers(): array
    {
        $statement = $this->connection->getConnection()->query(
            'SELECT id, username, password, email, role FROM user'
        );

        $users = [];
        while (($row = $statement->fetch())) {
            $user = $this->fetchUser($row);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * getUser
     *
     * @param int $userId
     *
     * @return User
     */
    public function getUser(int $userId): ?User
    {
        if (!is_int($userId)) {
            return null;
        }

        $statement = $this->connection->getConnection()->prepare(
            'SELECT id, username, password, email, role FROM user WHERE id = ?'
        );
        $statement->execute([$userId]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->fetchUser($row);
    }

    /**
     * setToken
     *
     * @param string $token
     * @param int    $userId
     *
     * @return bool
     */
    public function setToken(?string $token, int $userId): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE user SET token = ? WHERE id = ?'
        );
        $result = $statement->execute([$token, $userId]);

        return false !== $result;
    }

    /**
     * addUser
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @return bool
     */
    public function addUser(string $username, string $password, string $email): bool
    {
        $role = 'ROLE_USER';

        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO user(username, password, email, role) VALUES(?, ?, ?, ?)'
        );
        $affectedLines = $statement->execute([$username, MD5($password), $email, $role]);

        return ($affectedLines > 0);
    }

    /**
     * emailExists
     *
     * @param string $email
     *
     * @return bool
     */
    public function emailExists(string $email): int
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT EXISTS(SELECT 1 FROM user WHERE email = ?)'
        );
        $statement->execute([$email]);

        return $statement->fetchColumn();
    }

    /**
     * login
     *
     * @param string $email
     * @param string $password
     *
     * @return User|null
     */
    public function login(string $email, string $password): ?User
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, role FROM user WHERE email = ? AND password = ?"
        );

        $statement->execute([$email, md5($password)]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return $this->fetchUser($row);
    }

    /**
     * checkToken
     *
     * @param  string $token
     *
     * @return User|null
     */
    public function checkToken(string $token): ?User
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, role FROM user WHERE token = ?"
        );

        $statement->execute([$token]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return $this->fetchUser($row);
    }

    /**
     * fetchUser
     *
     * @param array $row
     *
     * @return User
     */
    private function fetchUser(array $row): User
    {
        $user = new User();
        $user->userId = $row['id'];
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->role = $row['role'];

        return $user;
    }
}
