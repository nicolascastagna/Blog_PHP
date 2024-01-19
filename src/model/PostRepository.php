<?php

namespace App\model;

use App\lib\DatabaseConnection;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPost(string $id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, user_id, title, chapo, content, DATE_FORMAT(creation_date, '%d/%m/%Y à %Hh%imin%ss') AS creation_date, DATE_FORMAT(update_date, '%d/%m/%Y à %Hh%imin%ss') AS update_date, image FROM post WHERE id = ?"
        );
        $statement->execute([$id]);

        $row = $statement->fetch();
        $post = new Post();
        $post->id = $row['id'];
        $post->userId = $row['user_id'];
        $post->title = $row['title'];
        $post->chapo = $row['chapo'];
        $post->content = $row['content'];
        $post->creationDate = $row['creation_date'];
        $post->updateDate = $row['update_date'];
        $post->image = $row['image'];

        return $post;
    }

    public function getPosts(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, user_id, title, chapo, content, DATE_FORMAT(creation_date, '%d/%m/%Y à %Hh%imin%ss') AS creation_date, DATE_FORMAT(update_date, '%d/%m/%Y à %Hh%imin%ss') AS update_date, image FROM post ORDER BY creation_date DESC LIMIT 0, 5"
        );

        $posts = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->id = $row['id'];
            $post->userId = $row['user_id'];
            $post->title = $row['title'];
            $post->chapo = $row['chapo'];
            $post->content = $row['content'];
            $post->creationDate = $row['creation_date'];
            $post->updateDate = $row['update_date'];
            $post->image = $row['image'];

            $posts[] = $post;
        }

        return $posts;
    }
}
