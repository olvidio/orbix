<?php

namespace Tests\integration\utils_database\infrastructure\persistence\postgresql;

use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;
use Tests\myTest;
use Tests\factories\utils_database\DbSchemaFactory;

class PgDbSchemaRepositoryTest extends myTest
{
    private DbSchemaRepositoryInterface $repository;
    private DbSchemaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
        $this->factory = new DbSchemaFactory();
    }

    public function test_guardar_nuevo_db_schema()
    {
        $oDbSchema = $this->factory->createSimple();
        $schema = $oDbSchema->getSchema();

        $result = $this->repository->Guardar($oDbSchema);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($schema);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($schema, $oGuardado->getSchema());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_db_schema_existente()
    {
        $oDbSchema = $this->factory->createSimple();
        $schema = $oDbSchema->getSchema();
        $this->repository->Guardar($oDbSchema);

        $oActualizado = $this->factory->createSimple($schema);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($schema);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oDbSchema = $this->factory->createSimple();
        $schema = $oDbSchema->getSchema();
        $this->repository->Guardar($oDbSchema);

        $oEncontrado = $this->repository->findById($schema);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(DbSchema::class, $oEncontrado);
        $this->assertEquals($schema, $oEncontrado->getSchema());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oDbSchema = $this->repository->findById('schema_inexistente_xyz_999');
        $this->assertNull($oDbSchema);
    }

    public function test_eliminar_db_schema()
    {
        $oDbSchema = $this->factory->createSimple();
        $schema = $oDbSchema->getSchema();
        $this->repository->Guardar($oDbSchema);

        $oGuardado = $this->repository->findById($schema);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($schema);
        $this->assertNull($oEliminado);
    }
}
