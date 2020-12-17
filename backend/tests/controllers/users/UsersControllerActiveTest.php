<?php

namespace WeeklyBuddy\Tests;

use Doctrine\ORM\NoResultException;
use WeeklyBuddy\Models\User;
use WeeklyBuddy\Tests\Utils\ResponseUtil;
use WeeklyBuddy\Tests\Utils\RequestUtil;
use WeeklyBuddy\Tests\controllers\users\AbstractUsersController;

/**
 * Tests suite for Users activation
 */
class UsersControllerActiveTest extends AbstractUsersController {
    /**
     * Tries to active an user without a token
     * @return void
     */
    public function test_activateAnUserWithoutToken(): void {
        $this->mockedJWTService->expects($this->never())
            ->method('isValid');
        $response = $this->usersController->activeUser(RequestUtil::createRequest('GET'));
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(400, $status);
        $this->assertEquals('Missing token.', $message);
    }

    /**
     * Tries to active an user with an invalid token
     * @return void
     */
    public function test_activateAnUserWithInvalidToken(): void {
        $this->mockedJWTService->method('isValid')->willReturn(false);
        $this->mockedJWTService->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo('notvalid'));

        $request = RequestUtil::createRequest('GET');
        $request = RequestUtil::addQueryParam($request, 'token', 'notvalid');
        $response = $this->usersController->activeUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(401, $status);
        $this->assertEquals('Connection refused.', $message);
    }

    /**
     * Tries to active a non existing user with a valid token
     * @return void
     */
    public function test_activateANonExistingUserWithValidToken(): void {
        $this->mockedJWTService->method('isValid')->willReturn(true);
        $this->mockedJWTService->method('getUserDTO')->willReturn(['user' => ['id' => 1]]);
        $this->mockedUsersService->method('activateUser')->will($this->throwException(new NoResultException()));

        $this->mockedJWTService->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo('valid'));
        $this->mockedJWTService->expects($this->once())
            ->method('getUserDTO')
            ->with($this->equalTo('valid'));
        $this->mockedUsersService->expects($this->once())
            ->method('activateUser')
            ->with($this->equalTo(1));

        $request = RequestUtil::createRequest('GET');
        $request = RequestUtil::addQueryParam($request, 'token', 'valid');
        $response = $this->usersController->activeUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(400, $status);
        $this->assertEquals('User associated to given token is invalid.', $message);
    }

    /**
     * Successfully actives an user with a valid token
     * @return void
     */
    public function test_activateAnUserWithValidToken(): void {
        $this->mockedJWTService->method('isValid')->willReturn(true);
        $this->mockedJWTService->method('getUserDTO')->willReturn(['user' => ['id' => 1]]);

        $this->mockedJWTService->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo('valid'));
        $this->mockedJWTService->expects($this->once())
            ->method('getUserDTO')
            ->with($this->equalTo('valid'));
        $this->mockedUsersService->expects($this->once())
            ->method('activateUser')
            ->with($this->equalTo(1));

        $request = RequestUtil::createRequest('GET');
        $request = RequestUtil::addQueryParam($request, 'token', 'valid');
        $response = $this->usersController->activeUser($request);
        $status = ResponseUtil::getStatusCode($response);
        $body = ResponseUtil::getBody($response);
        $message = json_decode($body, true)['message'];

        $this->assertEquals(200, $status);
        $this->assertEquals('User is now activated.', $message);
    }
}