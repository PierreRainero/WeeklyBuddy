<?php

namespace WeeklyBuddy\Services;

use Doctrine\ORM\{EntityManagerInterface, NoResultException, NonUniqueResultException};
use WeeklyBuddy\Exceptions\{AlreadyExistException, InvalidParameterException};
use WeeklyBuddy\Models\User;
use WeeklyBuddy\Services\EntityService;
use WeeklyBuddy\Services\Util\EmailService;

/**
 * This class is used to process to functional operations over "user" resource
 */
class UsersService extends EntityService {
    /**
     * Class name of the entity used (with namespace)
     * @var string
     */
    private $entityClassName = 'WeeklyBuddy\Models\User';

    /**
     * Service to check and send emails
     * @var EmailService
     */
    private $emailService;

	/**
     * Injected constructor
     * @param EntityManagerInterface $entityManager Object provided by the ORM to deal with entities
     * @param EmailService $emailService Instance of EmailService to deal with emails
     */
    public function __construct(EntityManagerInterface $entityManager, EmailService $emailService) {
        $this->emailService = $emailService;
        parent::__construct($entityManager);
    }
    
    /**
     * Adds a new user in the app if his email is not already taken
     * @param string $email Email for the new user
     * @param string $password Password for the new user
     * @param string $lang Language for the new user
     * @return User Persisted user
     * @throws AlreadyExistException
     * @throws NonUniqueResultException
     * @throws InvalidParameterException
     */
	public function add(string $email, string $password, string $lang): User {
        try {
            $this->findByEmail($email);
            throw new AlreadyExistException("An user with \"$email\" email already exists.");
        } catch(NoResultException $e) {
            if(!$this->emailService->emailIsValid($email)) {
                throw new InvalidParameterException('Email is invalid.');
            }
            $user = new User($email, password_hash($password, PASSWORD_BCRYPT));
            if(strlen($lang) === 2) {
                $user->setLang($lang);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $user;
        }
    }

    /**
     * Checks if a given password is correctly associated to a given user
     * @param User $user The user to check
     * @param string $password The password to check
     * @return bool "true" if passwords matched, "false" otherwise
     */
    public function userPasswordMatch(User $user, string $password): bool {
        return password_verify($password, $user->getPassword());
    }
    
    /**
     * Searches an user from his email
     * @param string $email Email used to search
     * @return User The found user
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findByEmail(string $email): User {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from($this->entityClassName, 'u')
            ->where('u.email = :emailParam')
            ->setParameter('emailParam', $email);
        $query = $queryBuilder->getQuery();
        $foundUser = $query->getSingleResult();
        return $foundUser;
    }

    /**
     * Searches an user from his unique identifier
     * @param int $id The user identifier to use
     * @return User  The found user
     * @throws NoResultException
     */
    public function findById(int $id): User {
        $foundUser = $this->entityManager->find($this->entityClassName, $id);
        if($foundUser === NULL){
            throw new NoResultException();
        }

        return $foundUser;
    }

    /**
     * Activates an user identified by his id
     * @param int $id The user identifier to use
     * @return void
     * @throws NoResultException
     */
    public function activateUser(int $id): void {
        $foundUser = $this->findById($id);
        $synchronizedUser = $this->entityManager->merge($foundUser);
        $synchronizedUser->setActive(true);
        $this->entityManager->flush();
    }
}
