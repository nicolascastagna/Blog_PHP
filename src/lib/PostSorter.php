<?php

namespace App\lib;

class PostSorter
{
    public static function sortByRecentDate(array $posts): array
    {
        usort($posts, function ($a, $b) {
            return strtotime($b->creationDate) - strtotime($a->creationDate);
        });

        return $posts;
    }
}
