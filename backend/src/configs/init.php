<?php

/**
 * First of all we import the autoload file provided by composer to use our required dependencies
 */
require_once(join(DIRECTORY_SEPARATOR, array(dirname(dirname(__DIR__)), 'vendor', 'autoload.php')));

use Dice\Dice;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * We list all the path to entities here
 */
$paths = array('../models/User.php');
$isDevMode = false;

/**
 * We retrieve the database configurations, that must be protected, to setup the ORM (@see Doctrine)
 */
$dbParams = include join(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(__DIR__))), 'env', 'db.php'));
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

/**
 * Then we setup the dependency injector (@see Dice)
 */
$dice = new Dice;
$dice = $dice->addRules([
    'WeeklyBuddy\Services\EntityService' => [
        /**
         * If an entity manager is required we provide the instance from Doctrine
         */
        'substitutions' =>
            [
                'Doctrine\ORM\EntityManagerInterface' => [Dice::INSTANCE => function() use ($entityManager) {
                    return $entityManager;
                }]
            ]
    ],
    // Shares frequently used services
    'WeeklyBuddy\Services\Util\EmailService' => ['shared' => true],
    'WeeklyBuddy\Services\Util\JWTService' => ['shared' => true]
]);