<?php

namespace WeeklyBuddy\Tests\Utils;

use flight\net\Request;

/**
 * Utilitary class to create flight\Request during tests
 */
abstract class RequestUtil {
    /**
     * Creates a json Request
     * @param string $method The HTTP method to set
     * @param string $rawBody The raw body to set
     * @return Request The request
     */
    public static function createRequest(string $method = 'GET', string $rawBody = ''): Request {
        $request = new Request();
        $request->method = $method;
        $request->data = json_decode($rawBody);
        return $request;
    }
    /**
     * Add a query parameter to a given request
     * @param Request $request The request object
     * @param string $name The parameter's name
     * @param string $value The parameter's value
     * @return Request The updated request
     */
    public static function addQueryParam(Request $request, string $name, string $value): Request {
        $params = $request->query->getData();
        $params += [ $name => $value ];
        $request->query->setData($params);
        return $request;
    }
}