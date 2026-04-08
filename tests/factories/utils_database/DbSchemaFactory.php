<?php

namespace Tests\factories\utils_database;

use src\utils_database\domain\entity\DbSchema;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;

/**
 * Factory para crear instancias de DbSchema para tests.
 * DbSchema usa el campo 'schema' como clave primaria (string).
 */
class DbSchemaFactory
{
    public function createSimple(?string $schema = null): DbSchema
    {
        $schema = $schema ?? 'test_schema_' . rand(1000, 9999);

        $oDbSchema = new DbSchema();
        $oDbSchema->setSchema($schema);
        $oDbSchema->setId(0);

        return $oDbSchema;
    }
}
