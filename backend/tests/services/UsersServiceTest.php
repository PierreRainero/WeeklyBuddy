<?php

namespace WeeklyBuddy\Tests;

use Dice\Dice;
use Doctrine\ORM\{EntityManagerInterface, EntityManager, QueryBuilder, AbstractQuery, NonUniqueResultException, NoResultException};
use PHPUnit\Framework\TestCase;
use WeeklyBuddy\Exceptions\{AlreadyExistException, InvalidParameterException};
use WeeklyBuddy\Models\User;
use WeeklyBuddy\Services\UsersService;
use WeeklyBuddy\Services\Util\EmailService;
use WeeklyBuddy\Tests\Utils\DoctrineUtil;

/**
 * Tests suite for Users service
 */
final class UsersServiceTest extends TestCase {
    private $mockedEmailService;
    private $mockedEntityManager;
    private $usersService;

    /**
     * Setups an UsersService for tests
     * @return void
     */
    public function setUp(): void {
        $dice = new Dice;
        $mockedEmailService = $this->createMock(EmailService::class);
        $mockedEntityManager = $this->createMock(EntityManager::class);
        $this->mockedEntityManager = $mockedEntityManager;
        $this->mockedEmailService = $mockedEmailService;
        $dice = $dice->addRules([
            'WeeklyBuddy\Services\UsersService' => [
                'substitutions' =>
                    [
                        'Doctrine\ORM\EntityManagerInterface' => [Dice::INSTANCE => function() use ($mockedEntityManager) {
                            return $mockedEntityManager;
                        }],
                        'WeeklyBuddy\Services\Util\EmailService' => [Dice::INSTANCE => function() use ($mockedEmailService) {
                            return $mockedEmailService;
                        }]
                    ]
            ]
        ]);
        $this->usersService = $dice->create('WeeklyBuddy\Services\UsersService');
    }

    /**
     * Tries to add an user with an already existing email
     * @return void
     */
    public function test_addAnAlreadyExistingUser(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        DoctrineUtil::setupQueryBuilder($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getSingleResult')->willReturn(new User('user@gmail.com', 'mypass'));

        $this->expectException(AlreadyExistException::class);
        $this->expectExceptionMessage('An user with "user@gmail.com" email already exists.');
        $this->usersService->add('user@gmail.com', 'mypass', 'en');
    }

    /**
     * Tries to add an user with an email used for multiple users
     * @return void
     */
    public function test_addAMultipleTimesAlreadyExistingUser(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        DoctrineUtil::setupQueryBuilder($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getSingleResult')->will($this->throwException(new NonUniqueResultException()));

        $this->expectException(NonUniqueResultException::class);
        $this->usersService->add('user@gmail.com', 'mypass', 'en');
    }

    /**
     * Tries to add an user with an invalid email
     * @return void
     */
    public function test_addAnUserWithInvalidEmail(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        DoctrineUtil::setupQueryBuilder($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getSingleResult')->will($this->throwException(new NoResultException()));
        $this->mockedEmailService->method('emailIsValid')->willReturn(false);

        $this->mockedEmailService->expects($this->once())
            ->method('emailIsValid')
            ->with($this->equalTo('standard string'));

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Email is invalid.');
        $this->usersService->add('standard string', 'mypass', 'en');
    }

    /**
     * Successfully adds a valid user
     * @return void
     */
    public function test_addAnUserWithSuccess(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        DoctrineUtil::setupQueryBuilder($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getSingleResult')->will($this->throwException(new NoResultException()));
        $this->mockedEmailService->method('emailIsValid')->willReturn(true);

        $this->mockedEmailService->expects($this->once())
            ->method('emailIsValid')
            ->with($this->equalTo('user@gmail.com'));

        $result = $this->usersService->add('user@gmail.com', 'mypass', 'fr');
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('user@gmail.com', $result->getEmail());
        $this->assertNotEquals('mypass', $result->getPassword());
        $this->assertFalse($result->isActive());
        $this->assertEquals('fr', $result->getLang());
    }

    /**
     * Adds a user with english language by default
     * @return void
     */
    public function test_addAnUserWithSuccessWithoutSpecifyLanguage(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        DoctrineUtil::setupQueryBuilder($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getSingleResult')->will($this->throwException(new NoResultException()));
        $this->mockedEmailService->method('emailIsValid')->willReturn(true);

        $this->mockedEmailService->expects($this->once())
            ->method('emailIsValid')
            ->with($this->equalTo('user@gmail.com'));

        $result = $this->usersService->add('user@gmail.com', 'mypass', '');
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('en', $result->getLang());
    }

    /**
     * An exception is thrown if a given id doesn't match with any user
     * @return void
     */
    public function test_exceptionWhenIdNotExisting(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('find')->willReturn(null);

        $this->expectException(NoResultException::class);
        $this->usersService->findById(1);
    }

    /**
     * Successfully retrieves an user from his id
     * @return void
     */
    public function test_findUserByHisId(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $this->mockedEntityManager->method('find')->willReturn(new User('user@gmail.com', 'mypass'));

        $result = $this->usersService->findById(1);
        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * Successfully activates an user
     * @return void
     */
    public function test_activateAnUser(): void {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $user = new User('user@gmail.com', 'mypass');
        $this->assertFalse($user->isActive());
        $this->mockedEntityManager->method('find')->willReturn($user);
        $this->mockedEntityManager->method('merge')->willReturn($user);

        $this->usersService->activateUser(1);
        $this->assertTrue($user->isActive());
    }
}

