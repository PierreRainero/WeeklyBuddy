<?php

namespace WeeklyBuddy\Exceptions;

use Exception;

/**
 * Class to throw when given parameter doesn't meet the required criteria
 */
class InvalidParameterException extends Exception {
    /**
     * Class contructor
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct(string $message, int $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}