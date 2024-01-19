<?php

namespace App\model;

class Post
{
    public int $id;
    public int $userId;
    public string $title;
    public string $chapo;
    public string $content;
    public string $creationDate;
    public ?string $updateDate;
    public ?string $image;
}
