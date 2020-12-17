<?php

namespace WeeklyBuddy\Exceptions;

/**
 * Class to transform any Exception to a correct HTTP response body
 */
class ExceptionMessage {
    private $uniqueCode;
    private $message;

    /**
     * Class contructor
     * @param string $uniqueCode
     * @param int $message
     */
    public function __construct(string $uniqueCode, string $message) {
        $this->uniqueCode = $uniqueCode;
        $this->message = $message;
    }

    /**
     * Transforms object to array in format for communications outside of the app 
     * @return array
     */
    public function toDTO(): array {
        return [
            'code'    => $this->uniqueCode,
            'message' => $this->message
        ];
    }
}