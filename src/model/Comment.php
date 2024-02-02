<?php

namespace App\model;

class Comment
{
    /**
     * @var int
     */
    public int $id;

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
     * @var string|null
     */
    public ?string $status;
}
