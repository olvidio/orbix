<?php

namespace Tests\integration\configuracion\infrastructure\repositories;

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use Tests\factories\configuracion\ConfigSchemaFactory;
use Tests\myTest;

class PgConfigSchemaRepositoryTest extends myTest
{
    private ConfigSchemaRepositoryInterface $repository;
    private ConfigSchemaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class);
        $this->factory = new ConfigSchemaFactory();
    }

    public function test_guardar_nuevo_configSchema()
    {
         // Crear instancia usando factory
        $oConfigSchema = $this->factory->createSimple();
        $id = $oConfigSchema->getParametro();

        // Guardar
        $result = $this->repository->Guardar($oConfigSchema);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oConfigSchemaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oConfigSchemaGuardado);
        $this->assertEquals($id, $oConfigSchemaGuardado->getParametro());

        // Limpiar
        $this->repository->Eliminar($oConfigSchemaGuardado);
    }

    public function test_actualizar_configSchema_existente()
    {
        // Crear y guardar instancia usando factory
        $oConfigSchema = $this->factory->createSimple();
        $id = $oConfigSchema->getParametro();
        $this->repository->Guardar($oConfigSchema);

        // Crear otra instancia con datos diferentes para actualizar
        $oConfigSchemaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oConfigSchemaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oConfigSchemaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oConfigSchemaActualizado);

        // Limpiar
        $this->repository->Eliminar($oConfigSchemaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oConfigSchema = $this->factory->createSimple();
        $id = $oConfigSchema->getParametro();
        $this->repository->Guardar($oConfigSchema);

        // Buscar por ID
        $oConfigSchemaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oConfigSchemaEncontrado);
        $this->assertInstanceOf(ConfigSchema::class, $oConfigSchemaEncontrado);
        $this->assertEquals($id, $oConfigSchemaEncontrado->getParametro());

        // Limpiar
        $this->repository->Eliminar($oConfigSchemaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oConfigSchema = $this->repository->findById($id_inexistente);

        $this->assertNull($oConfigSchema);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oConfigSchema = $this->factory->createSimple();
        $id = $oConfigSchema->getParametro();
        $this->repository->Guardar($oConfigSchema);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('parametro', $aDatos);
        $this->assertEquals($id, $aDatos['parametro']);

        // Limpiar
        $oConfigSchemaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oConfigSchemaParaborrar);
    }


}
