<?php

namespace WeeklyBuddy\Tests\Utils;

use Doctrine\ORM\QueryBuilder;

/**
 * Utilitary class to manage Doctrine's objects in tests
 */
abstract class DoctrineUtil {
    /**
     * Setups a mocked QueryBuilder to not crash in tests
     * @param QueryBuilder $queryBuilder The mocked QueryBuilder instance
     * @return void
     */
    public static function setupQueryBuilder(QueryBuilder $queryBuilder): void {
        $queryBuilder->method('select')->willReturn($queryBuilder);
        $queryBuilder->method('from')->willReturn($queryBuilder);
        $queryBuilder->method('where')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
    }
}