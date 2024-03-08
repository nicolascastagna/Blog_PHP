<?php

declare(strict_types=1);

namespace App\lib;

use Exception;

class CheckerId
{
    /**
     * getId
     *
     * Get the id from the argument
     *
     * @param array $args
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getId(array $args): int
    {
        if (isset($args['id']) && filter_var($args['id'], \FILTER_VALIDATE_INT) !== false && $args['id'] > 0) {
            return (int) $args['id'];
        }

        throw new Exception('Identifiant de post invalide !');
    }
}
