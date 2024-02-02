<?php

namespace App\model;

class Comment
{
    public int $id;
    public int $userId;
    public int $postId;
    public string $username;
    public string $content;
    public string $commentDate;
    public ?string $status;
}
