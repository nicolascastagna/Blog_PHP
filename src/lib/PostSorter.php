<?php

namespace App\lib;

class PostSorter
{
    /**
     * sortByRecentDate
     *
     * @param  array $posts
     * @return array
     */
    public static function sortByRecentDate(array $posts): array
    {
        usort($posts, function ($asc, $desc) {
            return strtotime($desc->creationDate) - strtotime($asc->creationDate);
        });

        return $posts;
    }
}
