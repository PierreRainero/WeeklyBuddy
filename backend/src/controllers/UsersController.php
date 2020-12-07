<?php

namespace WeeklyBuddy\Controllers;

use Doctrine\ORM\{NonUniqueResultException, NoResultException};
use Flight;
use flight\net\Request;
use WeeklyBuddy\Controllers\Controller;
use WeeklyBuddy\Exceptions\{AlreadyExistException, ExceptionMessage, EmailException, InvalidParameterException};
use WeeklyBuddy\Services\UsersService;
use WeeklyBuddy\Services\Util\{JWTService, EmailService};

/**
 * This class is used to manage any operations over the "user" resource from a request
 */
class UsersController extends Controller {
    /**
     * Service to process to functional operations over "user" resource
     * @var UsersService
     */
    private $usersService;

    /**
     * Service to check and send emails
     * @var EmailService
     */
    private $emailService;

    private $CONNECTION_REFUSED = 'Connection refused.';

    /**
     * Injected constructor
     * @param JWTService $jwtService Instance of JWTService to deal with JWT
     * @param UsersService $usersService Instance of UsersService to deal with users
     * @param EmailService $emailService Instance of EmailService to deal with emails
     */
    public function __construct(JWTService $jwtService, UsersService $usersService, EmailService $emailService) {
        $this->usersService = $usersService;
        $this->emailService = $emailService;
        parent::__construct($jwtService);
    }

    /**
     * Try to authentify a user passed through an HTTP request
     * @param Request $request HTTP Request containing the informations to authentify an user
     */
    public function authentifyUser(Request $request) {
        $jsonBody = json_decode($request->getBody(), true);

        if($this->emailOrPasswordAreMissing($jsonBody)) {
            return Flight::json(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }

        $emailToUse = $jsonBody['email'];
        $password = $jsonBody['password'];

        try {
            $user = $this->usersService->findByEmail($emailToUse);
            if(!$this->usersService->userPasswordMatch($user, $password)) {
                return Flight::json(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
            }
            $token = $this->jwtService->createUserToken($user);
            return Flight::json(['token' => $token], $this->HTTP_CODES['SUCCESS']);
        } catch(NonUniqueResultException $e) {
            return Flight::json((new ExceptionMessage('UC00', $this->generateNonUniqueErrorMessage($emailToUse)))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        } catch(NoResultException $e) {
            return Flight::json(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }
    }

    /**
     * Add a new user to the app from an HTTP request
     * @param Request $request HTTP Request containing the informations to create an user
     */
    public function addUser(Request $request) {
        $jsonBody = json_decode($request->getBody(), true);

        if($this->emailOrPasswordAreMissing($jsonBody)) {
            return Flight::json((new ExceptionMessage('UC10', 'Missing elements to create an user.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }
        
        $email = $jsonBody['email'];
        $password = $jsonBody['password'];
        $lang = array_key_exists('lang', $jsonBody) ? $jsonBody['lang'] : '';

        try {
            $user = $this->usersService->add($email, $password, $lang);
            $token = $this->jwtService->createUserToken($user);
            $this->emailService->sendActivationEmail($email, $token, $user->getLang());
            return Flight::json(['message' => 'Activation mail sent.'], 200);
        } catch(NonUniqueResultException $e) {
            return Flight::json((new ExceptionMessage('UC11', $this->generateNonUniqueErrorMessage($email)))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        } catch(AlreadyExistException | InvalidParameterException $e) {
            return Flight::json((new ExceptionMessage('UC12', $e->getMessage()))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        } catch(EmailException $e) {
            return Flight::json((new ExceptionMessage('UC13', $e->getMessage()))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        }
    }

    /**
     * Activate a given user through a valid token
     * @param Request $request HTTP Request containing the token as a query parameter
     */
    public function activeUser(Request $request) {
        $queryParams = $request->query;
        $token = $queryParams['token'];

        if($token === null) {
            return Flight::json((new ExceptionMessage('UC20', 'Missing token.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }

        if(!$this->jwtService->isValid($token)) {
            return Flight::json(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }

        $userDTO = $this->jwtService->getUserDTO($token);
        try {
            $this->usersService->activateUser($userDTO['user']['id']);
            return Flight::json(['message' => 'User is now activated.'], 200);
        } catch(NoResultException $e) {
            return Flight::json((new ExceptionMessage('UC21', 'User associated to given token is invalid.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }
    }

    /**
     * Check if the given body contain an email and a password
     * @param array $jsonBody JSON body to check
     * @return bool "true" if body is invalid, "false" otherwise
     */
    private function emailOrPasswordAreMissing(array $jsonBody): bool {
        return !array_key_exists('email', $jsonBody) || !array_key_exists('password', $jsonBody);
    }

    /**
     * Generate an error message to indicates it's not possible to retrieve an user from a given email
     * @param string $email Used email
     * @return string
     */
    private function generateNonUniqueErrorMessage(string $email): string {
        return "Impossible to correctly retrieve user with \"$email\" email, please contact an admnistrator and note given code.";
    }
}