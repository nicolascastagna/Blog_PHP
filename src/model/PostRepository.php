<?php

declare(strict_types=1);

namespace App\model;

use App\lib\DatabaseConnection;

class PostRepository
{
    /**
     * @var DatabaseConnection
     */
    public DatabaseConnection $connection;

    /**
     * getPost
     *
     * @param int $postId
     *
     * @return Post
     */
    public function getPost(int $postId): Post
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
        $statement->execute([$postId]);

        $row = $statement->fetch();

        if ($row === false) {
            return $this->fetchPost([]);
        }

        return $this->fetchPost($row);
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
                ORDER BY COALESCE(post.update_date, post.creation_date) DESC"
        );

        $rows = $statement->fetchAll();
        $posts = [];

        if ($rows === false) {
            return null;
        }

        foreach ($rows as $row) {
            $posts[] = $this->fetchPost($row);
        }

        return $posts;
    }

    /**
     * addPost
     *
     * @param int    $user_id
     * @param string $title
     * @param string $chapo
     * @param string $content
     * @param string|null $image
     *
     * @return bool
     */
    public function addPost(int $user_id, string $title, string $chapo, string $content, ?string $image): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO post(user_id, title, chapo, content, image, creation_date) VALUES(?, ?, ?, ?, ?, NOW())'
        );
        $affectedLines = $statement->execute([$user_id, $title, $chapo, $content, $image]);

        return ($affectedLines > 0);
    }

    /**
     * deletePost
     *
     * @param int $postId
     *
     * @return bool
     */
    public function deletePost(int $postId): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'DELETE FROM post WHERE id = ?'
        );
        $affectedLines = $statement->execute([$postId]);

        return ($affectedLines > 0);
    }

    /**
     * updatePost
     *
     * @param int    $postId
     * @param string $title
     * @param string $chapo
     * @param string $content
     *
     * @return bool
     */
    public function updatePost(int $postId, string $title, string $chapo, string $content): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE post SET title = ?, chapo = ?, content = ?, update_date = NOW() WHERE id = ?'
        );
        $affectedLines = $statement->execute([$title, $chapo, $content, $postId]);

        return ($affectedLines > 0);
    }

    /**
     * fetchPost
     *
     * @param array $row
     *
     * @return Post
     */
    private function fetchPost(array $row): Post
    {
        $post = new Post();
        $post->postId = ($row['id'] ?? 0);
        $post->userId = ($row['user_id'] ?? 0);
        $post->username = ($row['username'] ?? '');
        $post->title = ($row['title'] ?? '');
        $post->chapo = ($row['chapo'] ?? '');
        $post->content = ($row['content'] ?? '');
        $post->creationDate = ($row['creation_date'] ?? '');
        $post->updateDate = ($row['update_date'] ?? '');
        $post->image = ($row['image'] ?? '');

        return $post;
    }
}
