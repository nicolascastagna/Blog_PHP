<?php

namespace App\model;

use App\lib\DatabaseConnection;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getUsers(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, username, password, email, role FROM user"
        );

        $users = [];
        while (($row = $statement->fetch())) {
            $user = new User();
            $user->id = $row['id'];
            $user->username = $row['username'];
            $user->password = $row['password'];
            $user->email = $row['email'];
            $user->role = $row['role'];

            $users[] = $user;
        }

        return $users;
    }
}
