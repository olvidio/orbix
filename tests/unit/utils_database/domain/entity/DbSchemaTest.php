<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain\entity;

use src\utils_database\domain\entity\DbSchema;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;
use Tests\myTest;

final class DbSchemaTest extends myTest
{
    private DbSchema $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new DbSchema();
        $this->entity->setSchemaVo(new DbSchemaCode('public'));
        $this->entity->setIdVo(new DbSchemaId(5));
    }

    public function test_get_set_schema_vo(): void
    {
        $this->assertSame('public', $this->entity->getSchemaVo()->value());
        $this->assertSame('public', $this->entity->getSchema());
    }

    public function test_get_set_id_vo(): void
    {
        $this->assertSame(5, $this->entity->getIdVo()->value());
        $this->assertSame(5, $this->entity->getId());
    }
}
