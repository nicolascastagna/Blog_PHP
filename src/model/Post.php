<?php

declare(strict_types=1);

namespace App\model;

class Post
{
    /**
     * @var int
     */
    public int $postId;

    /**
     * @var int
     */
    public int $userId;

    /**
     * @var string
     */
    public string $username;

    /**
     * @var string
     */
    public string $title;

    /**
     * @var string
     */
    public string $chapo;

    /**
     * @var string
     */
    public string $content;

    /**
     * @var string
     */
    public string $creationDate;

    /**
     * @var string|null
     */
    public ?string $updateDate;

    /**
     * @var string|null
     */
    public ?string $image;
}
