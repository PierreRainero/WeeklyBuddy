<?php

namespace WeeklyBuddy\Exceptions;

use Exception;

/**
 * Class to throw when a entity already exist and can't be create again
 */
class AlreadyExistException extends Exception {
    /**
     * Class contructor
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(string $message, int $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}