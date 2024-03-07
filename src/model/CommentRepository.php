<?php

declare(strict_types=1);

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
     * @param int $postId
     *
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

        return $comments;
    }

    /**
     * getComment
     *
     * @param int $commentId
     *
     * @return Comment
     */
    public function getComment(int $commentId): Comment
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT comment.id, comment.user_id, comment.post_id, comment.content, status,
            DATE_FORMAT(comment.comment_date, '%d/%m/%Y à %Hh%imin%ss') AS comment_date
            FROM comment 
            WHERE comment.id = ?"
        );
        $statement->execute([$commentId]);

        $row = $statement->fetch();

        if ($row === false) {
            return $this->fetchComment([]);
        }

        return $this->fetchComment($row);
    }

    /**
     * getComments
     *
     * @return array
     */
    public function getWaitingComments(): array
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT comment.id, comment.user_id, comment.post_id, comment.content, status,
            DATE_FORMAT(comment.comment_date, '%d/%m/%Y à %Hh%imin%ss') AS comment_date, 
            user.username 
            FROM comment 
            LEFT JOIN user ON comment.user_id = user.id
            WHERE status = 0
            ORDER BY comment.comment_date DESC"
        );
        $statement->execute();

        $rows = $statement->fetchAll();
        $comments = [];

        if ($rows === false) {
            return null;
        }

        foreach ($rows as $row) {
            $comments[] = $this->fetchComment($row);
        }

        return $comments;
    }

    /**
     * updateCommentStatus
     *
     * @param int $commentId
     *
     * @return bool
     */
    public function updateCommentStatus(int $commentId): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE comment SET status = 1 WHERE id = ?'
        );
        $affectedLines = $statement->execute([$commentId]);

        return ($affectedLines > 0);
    }

    /**
     * addComment
     *
     * @param int    $user_id
     * @param int    $post_id
     * @param string $content
     *
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
     * @param array $row
     *
     * @return Comment
     */
    private function fetchComment(array $row): Comment
    {
        $comment = new Comment();
        $comment->commentId = ($row['id'] ?? 0);
        $comment->userId = ($row['user_id'] ?? 0);
        $comment->postId = ($row['post_id'] ?? 0);
        $comment->username = ($row['username'] ?? '');
        $comment->content = ($row['content'] ?? '');
        $comment->commentDate = ($row['comment_date'] ?? '');
        $comment->status = ($row['status'] ?? 0);

        return $comment;
    }
}
