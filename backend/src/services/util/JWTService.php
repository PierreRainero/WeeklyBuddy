<?php

namespace WeeklyBuddy\Services\Util;

use ReallySimpleJWT\{Build, Validate, Encode, Parse, Jwt};
use ReallySimpleJWT\Exception\ValidateException;
use WeeklyBuddy\Models\User;

/**
 * This class is an overlay of ReallySimpleJWT package to deal with Json Web Token
 */
class JWTService {
    /**
     * The secret used to encrypt tokens
     * @var string
     */
    private $JWTSecret;

    /**
     * Validity duration for emitted token (corresponding to one week)
     * @var int
     */
    private $validityDuration = 60 * 60 * 24 * 7;

    /**
     * Injected constructor
     */
    public function __construct() {
        $this->JWTSecret = include join(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(dirname(__DIR__)))), 'env', 'jwt.php'));
    }

    /**
     * Create a valid token for a given user
     * @param User $user User to user
     * @return string crypted token
     */
    public function createUserToken(User $user): string {
        $builder = new Build('JWT', new Validate(), new Encode());
        return $builder->setSecret($this->JWTSecret)
                       ->setExpiration(time() + $this->validityDuration)
                       ->setNotBefore(time() - 30)
                       ->setIssuedAt(time())
                       ->setPayloadClaim('user', $user->toDTO())
                       ->build()
                       ->getToken();
    }

    /**
     * Check if a given token is valid
     * @param string $token Token to check
     * @return bool "true" if the token is trustable and not expired, "false" otherwise
     */
    public function isValid(string $token): bool {
        $jwt = new Jwt($token, $this->JWTSecret);
        $parse = new Parse($jwt, new Validate(), new Encode());
        try {
            $parse->validate()
                  ->validateExpiration()
                  ->validateNotBefore()
                  ->parse();
            return true;
        } catch(ValidateException $error) {
            return false;
        }
    }

    /**
     *Extract an user (DTO format) from a token
     * @param string $token Token to use
     * @return array The user under array-values format
     */
    public function getUserDTO(string $token): array {
        $jwt = new Jwt($token, $this->JWTSecret);
        $parse = new Parse($jwt, new Validate(), new Encode());
        return $parse->parse()->getPayload();
    }
}