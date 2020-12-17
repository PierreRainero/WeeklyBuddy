<?php

namespace WeeklyBuddy\Tests;

use Doctrine\ORM\{NonUniqueResultException, NoResultException};
use WeeklyBuddy\Exceptions\{AlreadyExistException, EmailException, InvalidParameterException};
use WeeklyBuddy\Models\User;
use WeeklyBuddy\Tests\controllers\users\AbstractUsersController;
use WeeklyBuddy\Tests\Utils\ResponseUtil;
use WeeklyBuddy\Tests\Utils\RequestUtil;

/**
 * Tests suite for Users creation
 */
class UsersControllerAddTest extends AbstractUsersController {
    
    /**
     * Tries to add an user without any data in the request body
     * @return void
     */
    public function test_emptyBodyWhenAddingAnUser(): void {
        $response = $this->usersController->addUser(RequestUtil::createRequest('POST'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(400, $status);
        $this->assertEquals('Missing elements to create an user.', $message);
    }

    /**
     * Tries to add an user with missing email in the request body
     * @return void
     */
    public function test_missingEmailWhenAddingAnUser(): void {
        $response = $this->usersController->addUser(RequestUtil::createRequest('POST', '{"password": "mypass","lang": "en"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(400, $status);
        $this->assertEquals('Missing elements to create an user.', $message);
    }

    /**
     * Tries to add an user with missing password in the request body
     * @return void
     */
    public function test_missingPasswordWhenAddingAnUser(): void {
        $response = $this->usersController->addUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com","lang": "en"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(400, $status);
        $this->assertEquals('Missing elements to create an user.', $message);
    }

    /**
     * Tries to add an user with an email used for multiple users
     * @return void
     */
    public function test_addANewUserWhichAlreadyExistMultipleTimes(): void {
        $request = RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass","lang": "en"}');
        $this->mockedUsersService->method('add')->will($this->throwException(new NonUniqueResultException()));

        $this->mockedUsersService->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->equalTo('mypass'),
                $this->equalTo('en')
            );
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');
        $this->mockedEmailService->expects($this->never())
            ->method('sendActivationEmail');
        
        $response = $this->usersController->addUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(500, $status);
        $this->assertEquals('Impossible to correctly retrieve user with "user@gmail.com" email, please contact an admnistrator and note given code.', $message);
    }
    
    /**
     * Tries to add an user with an already used email
     * @return void
     */
    public function test_addANewUserWithAnAlreadyExistingEmail(): void {
        $request = RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass","lang": "en"}');
        $this->mockedUsersService->method('add')->will($this->throwException(new AlreadyExistException('An user with "user@gmail.com" email already exists.')));

        $this->mockedUsersService->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->equalTo('mypass'),
                $this->equalTo('en')
            );
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');
        $this->mockedEmailService->expects($this->never())
            ->method('sendActivationEmail');
        
        $response = $this->usersController->addUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(400, $status);
        $this->assertEquals('An user with "user@gmail.com" email already exists.', $message);
    }

    /**
     * Tries to add an user with an invalid email
     * @return void
     */
    public function test_addANewUserWithAnInvalidEmail(): void {
        $request = RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass","lang": "en"}');
        $this->mockedUsersService->method('add')->will($this->throwException(new InvalidParameterException('Email is invalid.')));

        $this->mockedUsersService->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->equalTo('mypass'),
                $this->equalTo('en')
            );
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');
        $this->mockedEmailService->expects($this->never())
            ->method('sendActivationEmail');
        
        $response = $this->usersController->addUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(400, $status);
        $this->assertEquals('Email is invalid.', $message);
    }

    /**
     * Tries to add an user with all data valid but with an exception thrown during the validation email sending
     * @return void
     */
    public function test_addANewUserWithValidationEmailInError(): void {
        $request = RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass","lang": "en"}');
        $enUser = new User('user@gmail.com', 'mypass');
        $enUser->setLang('en');
        $this->mockedUsersService->method('add')->willReturn($enUser);
        $this->mockedJWTService->method('createUserToken')->willReturn('token');
        $this->mockedEmailService->method('sendActivationEmail')->will($this->throwException(new EmailException('Activation email can\'t be sent.')));

        $this->mockedUsersService->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->equalTo('mypass'),
                $this->equalTo('en')
            );
        $this->mockedJWTService->expects($this->once())
            ->method('createUserToken');
        $this->mockedEmailService->expects($this->once())
            ->method('sendActivationEmail')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->anything(),
                $this->equalTo('en')
            );
        
        $response = $this->usersController->addUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(500, $status);
        $this->assertEquals('Activation email can\'t be sent.', $message);
    }

    /**
     * Successfully adds a new user
     * @return void
     */
    public function test_addANewUserWithSuccess(): void {
        $request = RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass","lang": "fr"}');
        $frUser = new User('user@gmail.com', 'mypass');
        $frUser->setLang('fr');
        $this->mockedUsersService->method('add')->willReturn($frUser);
        $this->mockedJWTService->method('createUserToken')->willReturn('token');

        $this->mockedUsersService->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->equalTo('mypass'),
                $this->equalTo('fr')
            );
        $this->mockedJWTService->expects($this->once())
            ->method('createUserToken');
        $this->mockedEmailService->expects($this->once())
            ->method('sendActivationEmail')
            ->with(
                $this->equalTo('user@gmail.com'),
                $this->anything(),
                $this->equalTo('fr')
            );
        
        $response = $this->usersController->addUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(200, $status);
        $this->assertEquals('Activation mail sent.', $message);
    }
}