<?php

namespace WeeklyBuddy\Exceptions;

/**
 * Class to transform any Exception to a correct HTTP response body
 */
class ExceptionMessage {
    private $uniqueCode;
    private $message;
    private $fieldsInError;

    /**
     * Class contructor
     * @param string $uniqueCode
     * @param int $message
     * @param array $fieldsInError
     */
    public function __construct(string $uniqueCode, string $message, array $fieldsInError = []) {
        $this->uniqueCode = $uniqueCode;
        $this->message = $message;
        $this->fieldsInError = $fieldsInError;
    }

    /**
     * Transforms object to array in format for communications outside of the app 
     * @return array
     */
    public function toDTO(): array {
        return [
            'code'          => $this->uniqueCode,
            'message'       => $this->message,
            'fieldsInError' => $this->fieldsInError
        ];
    }
}