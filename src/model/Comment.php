<?php

declare(strict_types=1);

namespace App\model;

class Comment
{
    /**
     * @var int
     */
    public int $commentId;

    /**
     * @var int
     */
    public int $userId;

    /**
     * @var int
     */
    public int $postId;

    /**
     * @var string
     */
    public string $username;

    /**
     * @var string
     */
    public string $content;

    /**
     * @var string
     */
    public string $commentDate;

    /**
     * @var int|null
     */
    public ?int $status;
}
