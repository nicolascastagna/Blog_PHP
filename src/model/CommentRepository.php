<?php

namespace App\model;

use App\lib\DatabaseConnection;

class CommentRepository
{

    /**
     * @var DatabaseConnection
     */
    public DatabaseConnection $connection;

    /**
     * getComments
     *
     * @param  int $postId
     * @return array
     */
    public function getComments(int $postId): array
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT comment.id, comment.user_id, comment.post_id, comment.content, status,
            DATE_FORMAT(comment.comment_date, '%d/%m/%Y à %Hh%imin%ss') AS comment_date, 
            user.username 
            FROM comment 
            LEFT JOIN user ON comment.user_id = user.id
            WHERE comment.post_id = ?
            ORDER BY comment.comment_date DESC"
        );
        $statement->execute([$postId]);

        $rows = $statement->fetchAll();
        $comments = [];

        foreach ($rows as $row) {
            $comments[] = $this->fetchComment($row);
        }
        // var_dump($comments);
        return $comments;
    }

    /**
     * addComment
     *
     * @param  int $user_id
     * @param  int $post_id
     * @param  string $content
     * @return bool
     */
    public function addComment(int $user_id, int $post_id, string $content): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO comment(user_id, post_id, content, comment_date) VALUES(?, ?, ?, NOW())'
        );
        $affectedLines = $statement->execute([$user_id, $post_id, $content]);

        return ($affectedLines > 0);
    }

    /**
     * fetchComment
     *
     * @param  array $row
     * @return Comment
     */
    private function fetchComment(array $row): Comment
    {
        $comment = new Comment();
        $comment->id = $row['id'];
        $comment->userId = $row['user_id'];
        $comment->postId = $row['post_id'];
        $comment->username = $row['username'];
        $comment->content = $row['content'];
        $comment->commentDate = $row['comment_date'];
        $comment->status = $row['status'];

        return $comment;
    }
}
