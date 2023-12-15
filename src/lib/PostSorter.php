<?php

namespace App\lib;

class PostSorter
{
    public static function sortByModificationDate(array $posts): array
    {
        usort($posts, function ($a, $b) {
            return strtotime($b['creation_date']) - strtotime($a['creation_date']);
        });

        return $posts;
    }
}
