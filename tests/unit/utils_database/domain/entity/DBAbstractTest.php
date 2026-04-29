<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain\entity;

use ReflectionClass;
use src\utils_database\domain\entity\DBAbstract;
use Tests\myTest;

final class DBAbstractTest extends myTest
{
    public function test_class_is_abstract(): void
    {
        $this->assertTrue((new ReflectionClass(DBAbstract::class))->isAbstract());
    }

    public function test_hasServerSelect_returns_boolean(): void
    {
        $result = DBAbstract::hasServerSelect();
        $this->assertIsBool($result);
    }
}
