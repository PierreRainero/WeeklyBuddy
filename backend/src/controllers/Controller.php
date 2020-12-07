<?php

namespace WeeklyBuddy\Controllers;

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
}