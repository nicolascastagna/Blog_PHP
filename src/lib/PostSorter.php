<?php

declare(strict_types=1);

namespace App\lib;

class PostSorter
{
    /**
     * sortByRecentDate
     *
     * @param array $posts
     *
     * @return array
     */
    public static function sortByRecentDate(array $posts): array
    {
        usort($posts, fn ($asc, $desc) => (strtotime($desc->creationDate) - strtotime($asc->creationDate)));

        return $posts;
    }
}
