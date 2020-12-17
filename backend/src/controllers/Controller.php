<?php

namespace WeeklyBuddy\Controllers;

use Flight;
use flight\net\Response;
use WeeklyBuddy\Services\Util\JWTService;

/**
 * The parent class for every controller
 */
class Controller {
    /**
     * Service to deal with Json Web Token (create, validate and decode them)
     * @var JWTService 
     */
    protected $jwtService;

    /**
     * All used HTTP codes
     * @var array
     */
    protected $HTTP_CODES = [
        'SUCCESS' => 200,
        'BAD_REQUEST' => 400,
        'UNAUTHORIZED' => 401,
        'INTERNAL_SERVER_ERROR' => 500
    ];

    /**
     * Injected constructor
     * @param JWTService $jwtService Instance of JWTService to deal with JWT
     */
    public function __construct(JWTService $jwtService) {
        $this->jwtService = $jwtService;
    }

    /**
     * Setups the response object with json body
     * @param array $body Body as array to convert into json object
     * @param int $status HTTP status to set for the response
     * @return Response
     */
    protected function jsonResponse(array $body, int $status = 200): Response {
        return Flight::response()
            ->status($status)
            ->header('Content-Type', 'application/json; charset=utf-8')
            ->write(json_encode($body));
    }
}