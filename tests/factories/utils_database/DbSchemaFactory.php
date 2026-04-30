<?php

namespace Tests\factories\utils_database;

use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;

/**
 * Factory para crear instancias de DbSchema para tests.
 * La tabla `db_idschema` usa columnas `schema` (texto) e `id` (entero positivo o -1001 especial).
 */
class DbSchemaFactory
{
    public function createSimple(?string $schema = null): DbSchema
    {
        $repository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);

        $schemaVal = $schema ?? ('test_schema_' . random_int(1000, 9999));
        $existente = $repository->findById($schemaVal);
        $id = $existente !== null ? $existente->getId() : $repository->getNewId();

        $oDbSchema = new DbSchema();
        $oDbSchema->setSchema($schemaVal);
        $oDbSchema->setId($id);

        return $oDbSchema;
    }
}
