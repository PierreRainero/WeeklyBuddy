<?php

namespace WeeklyBuddy\Tests;

use Doctrine\ORM\{NonUniqueResultException, NoResultException};
use WeeklyBuddy\Models\User;
use WeeklyBuddy\Tests\Utils\ResponseUtil;
use WeeklyBuddy\Tests\Utils\RequestUtil;
use WeeklyBuddy\Tests\controllers\users\AbstractUsersController;

/**
 * Tests suite for Users authentication
 */
class UsersControllerAuthentifyTest extends AbstractUsersController {
    /**
     * Tries to authentify an user without any data in the request body
     * @return void
     */
    public function test_emptyBodyWhenAuthentifyingAnUser(): void {
        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }


    /**
     * Tries to authentify an user with missing email in the request body
     * @return void
     */
    public function test_missingEmailWhenAuthentifyingAnUser(): void {
        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"password": "mypass"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }

    /**
     * Tries to authentify an user with missing password in the request body
     * @return void
     */
    public function test_missingPasswordWhenAuthentifyingAnUser(): void {
        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }

    /**
     * Tries to authentify an user with an email used for multiple users which isn't allowed
     * @return void
     */
    public function test_authentifyAnUserWhichExistMultipleTimes(): void {
        $this->mockedUsersService->method('findByEmail')->will($this->throwException(new NonUniqueResultException()));

        $this->mockedUsersService->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo('user@gmail.com'));
        $this->mockedUsersService->expects($this->never())
            ->method('userPasswordMatch');
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');

        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(500, $status);
        $this->assertEquals('Impossible to correctly retrieve user with "user@gmail.com" email, please contact an admnistrator and note given code.', $message);
    }

    /**
     * Tries to authentify an user which not exist
     * @return void
     */
    public function test_authentifyAnUserWhichNotExist(): void {
        $this->mockedUsersService->method('findByEmail')->will($this->throwException(new NoResultException()));

        $this->mockedUsersService->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo('user@gmail.com'));
        $this->mockedUsersService->expects($this->never())
            ->method('userPasswordMatch');
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');

        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }

    /**
     * Tries to authentify an user with the wrong password
     * @return void
     */
    public function test_authentifyAnUserWithWrongPassword(): void {
        $user = new User('user@gmail.com', 'mypass');
        $this->mockedUsersService->method('findByEmail')->willReturn($user);
        $this->mockedUsersService->method('userPasswordMatch')->willReturn(false);

        $this->mockedUsersService->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo('user@gmail.com'));
        $this->mockedUsersService->expects($this->once())
            ->method('userPasswordMatch')
            ->with(
                $this->equalTo($user),
                $this->equalTo('notmypass')
            );
        $this->mockedJWTService->expects($this->never())
            ->method('createUserToken');

        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "notmypass"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];
        
        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }

    /**
     * Authentify an user with the correct password
     * @return void
     */
    public function test_authentifyAnUserWithCorrectPassword(): void {
        $user = new User('user@gmail.com', 'mypass');
        $this->mockedUsersService->method('findByEmail')->willReturn($user);
        $this->mockedUsersService->method('userPasswordMatch')->willReturn(true);
        $this->mockedJWTService->method('createUserToken')->willReturn('token');

        $this->mockedUsersService->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo('user@gmail.com'));
        $this->mockedUsersService->expects($this->once())
            ->method('userPasswordMatch')
            ->with(
                $this->equalTo($user),
                $this->equalTo('mypass')
            );
        $this->mockedJWTService->expects($this->once())
            ->method('createUserToken');

        $response = $this->usersController->authentifyUser(RequestUtil::createRequest('POST', '{"email": "user@gmail.com","password": "mypass"}'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $token = json_decode($body, true)['token'];
        
        $this->assertEquals(200, $status);
        $this->assertEquals('token', $token);
    }
}