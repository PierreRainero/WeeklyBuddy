<?php

namespace WeeklyBuddy\Models;

use Doctrine\ORM\Mapping\{Entity, Table, Id, Column, GeneratedValue};

/**
 * @Entity
 * @Table(name="users")
 */
class User {
    /**
     * The user identifier
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * The user mail, used as functional identifier
     * @Column(type="string")
     * @var string
     */
    private $email;

    /**
     * The user password used to access to the app
     * @Column(type="string")
     * @var string
     */
    private $password;

    /**
     * The user language
     * @Column(type="string")
     * @var string
     */
    private $lang = 'en';

    /**
     * @Column(type="boolean")
     * @var bool
     */
    private $active = false;

    /**
     * Class contructor
     * @param string $email The user email
     * @param string $password The user password
     */
    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Id getter
     * @return int The user identifier
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Email getter
     * @return string The user mail
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Email setter
     * @param string $email The new user mail to use
     * @return void
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * Password getter
     * @return string The user password
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * Password setter
     * @param string $password The new user password to use
     * @return void
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    /**
     * Active getter
     * @return bool The user mail
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * Active setter
     * @param bool $active The new user status
     * @return void
     */
    public function setActive(bool $active): void {
        $this->active = $active;
    }

    /**
     * Language getter
     * @return string The user language
     */
    public function getLang(): string {
        return $this->lang;
    }

    /**
     * Language setter
     * @param string $lang The new user language to use
     * @return void
     */
    public function setLang(string $lang): void {
        $this->lang = $lang;
    }

    /**
     * Transforms object to array in format for communications outside of the app 
     * @return array
     */
    public function toDTO(): array {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'lang'  => $this->lang
        ];
    }
}