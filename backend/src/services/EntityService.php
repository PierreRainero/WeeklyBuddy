<?php

namespace WeeklyBuddy\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * The parent class for every service dealing with entities
 */
class EntityService {
    /**
     * @var EntityManagerInterface Object provided by the ORM to deal with entities
     */
    protected $entityManager;

    /**
     * Injected constructor
     * @param EntityManagerInterface $entityManager Object provided by the ORM to deal with entities
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
}