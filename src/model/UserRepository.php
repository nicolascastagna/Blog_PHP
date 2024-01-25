<?php

namespace App\model;

use App\lib\DatabaseConnection;

class UserRepository
{
    public DatabaseConnection $connection;

    public function getUsers(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, username, password, email, role FROM user"
        );

        $users = [];
        while (($row = $statement->fetch())) {
            $user = $this->fetchUser($row);
            $users[] = $user;
        }

        return $users;
    }

    public function getUser(int $id): ?User
    {
        if (!is_int($id)) {
            return null;
        }

        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, password, email, role FROM user WHERE id = ?"
        );
        $statement->execute([$id]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return $this->fetchUser($row);
    }

    private function fetchUser(array $row): User
    {
        $user = new User();
        $user->id = $row['id'];
        $user->username = $row['username'];
        $user->password = $row['password'];
        $user->email = $row['email'];
        $user->role = $row['role'];

        return $user;
    }
}
