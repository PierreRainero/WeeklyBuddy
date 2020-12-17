<?php

namespace WeeklyBuddy\Tests\Utils;

use flight\net\Response;

/**
 * Utilitary class to read flight\Response during tests
 */
abstract class ResponseUtil {
    /**
     * Extracts the status code of a given response
     * @param Response $response The response to parse
     * @return int The status code
     */
    public static function getStatusCode(Response $response): int {
        return ResponseUtil::getProtectedValue($response, 'status');
    }

    /**
     * Extracts the body of a given response
     * @param Response $response The response to parse
     * @return string The raw body
     */
    public static function getBody(Response $response): string {
        return ResponseUtil::getProtectedValue($response, 'body');
    }

    /**
     * Extracts a property of a given response
     * @param Response $response The response to parse
     * @param string $propertyName The property name to extract
     * @return The property value
     */
    private static function getProtectedValue(Response $response, string $propertyName) {
        $reflectionClass = new \ReflectionClass($response);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($response);
    }
}