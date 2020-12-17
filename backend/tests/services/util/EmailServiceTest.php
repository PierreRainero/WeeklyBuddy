<?php

namespace WeeklyBuddy\Tests;

use Dice\Dice;
use PHPUnit\Framework\TestCase;

/**
 * Tests suite for Email service
 */
final class EmailServiceTest extends TestCase {
    private $emailService;

    /**
     * Setup an EmailService for tests
     * @return void
     */
    public function setUp(): void {
        $dice = new Dice;
        $this->emailService = $dice->create('WeeklyBuddy\Services\Util\EmailService');
    }

    /**
     * An email respecting the regex with a correct DNS should be considered as valid
     * @return void
     */
    public function test_ValidEmail(): void {
        $this->assertTrue($this->emailService->emailIsValid('user@gmail.com'));
    }

    /**
     * A string that not respecting the regex should be invalid
     * @return void
     */
    public function test_InvalidEmail(): void {
        $this->assertFalse($this->emailService->emailIsValid('standard string'));
    }

    /**
     * A non existing DNS should be invalid
     * @return void
     */
    public function test_InvalidDns(): void {
        $this->assertFalse($this->emailService->emailIsValid('user@qsdsqd.com'));
    }
}

