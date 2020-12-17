<?php

namespace WeeklyBuddy\Tests\controllers\users;

use Flight;
use Dice\Dice;
use PHPUnit\Framework\TestCase;
use WeeklyBuddy\Services\UsersService;
use WeeklyBuddy\Services\Util\{JWTService, EmailService};

/**
 * Abstract class to setup tests suites for users controller
 */
abstract class AbstractUsersController extends TestCase {
    protected $mockedUsersService;
    protected $mockedJWTService;
    protected $mockedEmailService;
    protected $usersController;

    /**
     * Setups Users' controller with mocked lower layers and clean-up the current response
     * @return void
     */
    public function setUp(): void {
        $dice = new Dice;
        $mockedUsersService = $this->createMock(UsersService::class);
        $mockedJWTService = $this->createMock(JWTService::class);
        $mockedEmailService = $this->createMock(EmailService::class);
        $this->mockedUsersService = $mockedUsersService;
        $this->mockedJWTService = $mockedJWTService;
        $this->mockedEmailService = $mockedEmailService;
        $dice = $dice->addRules([
            'WeeklyBuddy\Controllers\UsersController' => [
                'substitutions' =>
                    [
                        'WeeklyBuddy\Services\UsersService' => [Dice::INSTANCE => function() use ($mockedUsersService) {
                            return $mockedUsersService;
                        }],
                        'WeeklyBuddy\Services\Util\JWTService' => [Dice::INSTANCE => function() use ($mockedJWTService) {
                            return $mockedJWTService;
                        }],
                        'WeeklyBuddy\Services\Util\EmailService' => [Dice::INSTANCE => function() use ($mockedEmailService) {
                            return $mockedEmailService;
                        }]
                    ]
            ]
        ]);
        $this->usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
        Flight::response()->clear();
    }
}