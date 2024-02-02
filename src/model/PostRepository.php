<?php

namespace App\model;

use App\lib\DatabaseConnection;

class PostRepository
{
    public DatabaseConnection $connection;

    /**
     * getPost
     *
     * @param  int $id
     * @return Post
     */
    public function getPost(int $id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT post.id, post.user_id, post.title, post.chapo, post.content, 
                DATE_FORMAT(post.creation_date, '%d/%m/%Y à %Hh%imin%ss') AS creation_date, 
                DATE_FORMAT(post.update_date, '%d/%m/%Y à %Hh%imin%ss') AS update_date, post.image, 
                user.username 
                FROM post 
                LEFT JOIN user ON post.user_id = user.id
                WHERE post.id = ?"
        );
        $statement->execute([$id]);

        $row = $statement->fetch();
        $post = $this->fetchPost($row);

        return $post;
    }

    /**
     * getPosts
     *
     * @return array
     */
    public function getPosts(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT post.id, post.user_id, post.title, post.chapo, post.content, 
                DATE_FORMAT(post.creation_date, '%d/%m/%Y à %Hh%imin%ss') AS creation_date, 
                DATE_FORMAT(post.update_date, '%d/%m/%Y à %Hh%imin%ss') AS update_date, post.image, 
                user.username 
                FROM post 
                LEFT JOIN user ON post.user_id = user.id
                ORDER BY post.creation_date DESC"
        );

        $rows = $statement->fetchAll();
        $posts = [];

        foreach ($rows as $row) {
            $posts[] = $this->fetchPost($row);
        }
        return $posts;
    }

    /**
     * addPost
     *
     * @param  int $user_id
     * @param  string $title
     * @param  string $chapo
     * @param  string $content
     * @return bool
     */
    public function addPost(int $user_id, string $title, string $chapo, string $content): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO post(user_id, title, chapo, content, creation_date) VALUES(?, ?, ?, ?, NOW())'
        );
        $affectedLines = $statement->execute([$user_id, $title, $chapo, $content]);

        return ($affectedLines > 0);
    }

    /**
     * deletePost
     *
     * @param  int $id
     * @return bool
     */
    public function deletePost(int $id): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'DELETE FROM post WHERE id = ?'
        );
        $affectedLines = $statement->execute([$id]);

        return ($affectedLines > 0);
    }

    /**
     * updatePost
     *
     * @param  int $id
     * @param  string $title
     * @param  string $chapo
     * @param  string $content
     * @return bool
     */
    public function updatePost(int $id, string $title, string $chapo, string $content): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE post SET title = ?, chapo = ?, content = ?, update_date = NOW() WHERE id = ?'
        );
        $affectedLines = $statement->execute([$title, $chapo, $content, $id]);

        return ($affectedLines > 0);
    }

    /**
     * fetchPost
     *
     * @param  array $row
     * @return Post
     */
    private function fetchPost(array $row): Post
    {
        $post = new Post();
        $post->id = $row['id'];
        $post->userId = $row['user_id'];
        $post->username = $row['username'];
        $post->title = $row['title'];
        $post->chapo = $row['chapo'];
        $post->content = $row['content'];
        $post->creationDate = $row['creation_date'];
        $post->updateDate = $row['update_date'];
        $post->image = $row['image'];

        return $post;
    }
}
