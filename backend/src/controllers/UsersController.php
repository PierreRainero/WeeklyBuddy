<?php

namespace WeeklyBuddy\Controllers;

use Doctrine\ORM\{NonUniqueResultException, NoResultException};
use flight\net\Request;
use flight\net\Response;
use WeeklyBuddy\Controllers\Controller;
use WeeklyBuddy\Exceptions\{AlreadyExistException, ExceptionMessage, EmailException, InvalidParameterException, ValidationError};
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
     * Tries to authentify a user passed through an HTTP request
     * @param Request $request HTTP Request containing the informations to authentify an user
     * @return Response
     */
    public function authentifyUser(Request $request): Response {
        $jsonBody = $request->data;

        if($jsonBody === null || $this->emailOrPasswordAreMissing($jsonBody)) {
            return $this->jsonResponse(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }

        $emailToUse = $jsonBody->email;
        $password = $jsonBody->password;

        try {
            $user = $this->usersService->findByEmail($emailToUse);
            if(!$this->usersService->userPasswordMatch($user, $password)) {
                return $this->jsonResponse(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
            }
            $token = $this->jwtService->createUserToken($user);
            return $this->jsonResponse(['token' => $token], $this->HTTP_CODES['SUCCESS']);
        } catch(NonUniqueResultException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC00', $this->generateNonUniqueErrorMessage($emailToUse)))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        } catch(NoResultException $e) {
            return $this->jsonResponse(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }
    }

    /**
     * Adds a new user to the app from an HTTP request
     * @param Request $request HTTP Request containing the informations to create an user
     * @return Response
     */
    public function addUser(Request $request): Response {
        $jsonBody = $request->data;
        
        if($jsonBody === null || $this->emailOrPasswordAreMissing($jsonBody)) {
            return $this->jsonResponse((new ExceptionMessage('UC10', 'Missing elements to create an user.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }
        
        $email = $jsonBody->email;
        $password = $jsonBody->password;
        $lang = $jsonBody->lang !== null ? $jsonBody->lang : '';

        try {
            $user = $this->usersService->add($email, $password, $lang);
            $token = $this->jwtService->createUserToken($user);
            $this->emailService->sendActivationEmail($email, $token, $user->getLang());
            return $this->jsonResponse(['message' => 'Activation mail sent.'], 200);
        } catch(NonUniqueResultException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC11', $this->generateNonUniqueErrorMessage($email)))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        } catch(AlreadyExistException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC13', $e->getMessage(), ['email' => ValidationError::AlreadyExist]))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        } catch(InvalidParameterException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC14', $e->getMessage(), ['email' => ValidationError::Invalid]))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        } catch(EmailException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC15', $e->getMessage()))->toDTO(), $this->HTTP_CODES['INTERNAL_SERVER_ERROR']);
        }
    }

    /**
     * Activates a given user through a valid token
     * @param Request $request HTTP Request containing the token as a query parameter
     * @return Response
     */
    public function activeUser(Request $request): Response {
        $queryParams = $request->query;
        $token = $queryParams['token'];

        if($token === null) {
            return $this->jsonResponse((new ExceptionMessage('UC20', 'Missing token.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }

        if(!$this->jwtService->isValid($token)) {
            return $this->jsonResponse(['message' => $this->CONNECTION_REFUSED], $this->HTTP_CODES['UNAUTHORIZED']);
        }

        $userDTO = $this->jwtService->getUserDTO($token);
        try {
            $this->usersService->activateUser($userDTO['user']['id']);
            return $this->jsonResponse(['message' => 'User is now activated.'], 200);
        } catch(NoResultException $e) {
            return $this->jsonResponse((new ExceptionMessage('UC21', 'User associated to given token is invalid.'))->toDTO(), $this->HTTP_CODES['BAD_REQUEST']);
        }
    }

    /**
     * Checks if the given body contain an email and a password
     * @param object $jsonBody JSON body to check
     * @return bool "true" if body is invalid, "false" otherwise
     */
    private function emailOrPasswordAreMissing(object $jsonBody): bool {
        return !isset($jsonBody->email) || !isset($jsonBody->password);
    }

    /**
     * Generates an error message to indicates it's not possible to retrieve an user from a given email
     * @param string $email Used email
     * @return string
     */
    private function generateNonUniqueErrorMessage(string $email): string {
        return "Impossible to correctly retrieve user with \"$email\" email, please contact an admnistrator and note given code.";
    }
}