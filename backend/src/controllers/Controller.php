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
     * All authorized DNS
     * @var array
     */
    private $dns_allowed= ['https://weeklybuddy.pierre-rainero.fr', 'https://www.weeklybuddy.pierre-rainero.fr', 'http://weeklybuddy.pierre-rainero.fr', 'http://www.weeklybuddy.pierre-rainero.fr'];

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
        $response = Flight::response()
            ->status($status)
            ->header('Content-Type', 'application/json;charset=utf-8');

        if(!empty($body)) {
            $response->write(json_encode($body));
        }

        if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $this->dns_allowed)){
            $header_origin = $_SERVER['HTTP_ORIGIN'];
            $response->header('Access-Control-Allow-Origin', $header_origin);
        }

        return $response;
    }

    /**
     * Create a valid preflight response
     * @return Response
     */
    public function preflightRequest(): Response {
        $response = $this->jsonResponse([]);
        $response->header('Access-Control-Allow-Headers', 'Content-Type, x-requested-with');
        return $response;
    }
}