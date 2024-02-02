<?php

namespace App\lib;

use Exception;

class PostIdChecker
{
    /**
     * getId
     * 
     * Get the id from the argument
     *
     * @param  array $args
     * @throws Exception
     * @return int
     */
    public static function getId(array $args): int
    {
        if (isset($args['id']) && filter_var($args['id'], FILTER_VALIDATE_INT) !== false && $args['id'] > 0) {
            return (int) $args['id'];
        } else {
            throw new Exception('Identifiant de post invalide !');
        }
    }
}
