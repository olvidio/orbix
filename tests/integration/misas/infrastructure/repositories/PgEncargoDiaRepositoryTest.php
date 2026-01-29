<?php

namespace Tests\integration\misas\infrastructure\repositories;

use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use Tests\myTest;
use Tests\factories\misas\EncargoDiaFactory;

class PgEncargoDiaRepositoryTest extends myTest
{
    private EncargoDiaRepositoryInterface $repository;
    private EncargoDiaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $this->factory = new EncargoDiaFactory();
    }

    public function test_guardar_nuevo_encargoDia()
    {
        // Crear instancia usando factory
        $oEncargoDia = $this->factory->createSimple();
        $id = $oEncargoDia->getUuidItemVo();

        // Guardar
        $result = $this->repository->Guardar($oEncargoDia);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoDiaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoDiaGuardado);
        $this->assertEquals($id, $oEncargoDiaGuardado->getUuidItemVo());

        // Limpiar
        $this->repository->Eliminar($oEncargoDiaGuardado);
    }

    public function test_actualizar_encargoDia_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoDia = $this->factory->createSimple();
        $id = $oEncargoDia->getUuidItemVo();
        $this->repository->Guardar($oEncargoDia);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoDiaUpdated = $this->factory->create($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoDiaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoDiaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoDiaActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoDiaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoDia = $this->factory->createSimple();
        $id = $oEncargoDia->getUuidItemVo();
        $this->repository->Guardar($oEncargoDia);

        // Buscar por ID
        $oEncargoDiaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoDiaEncontrado);
        $this->assertInstanceOf(EncargoDia::class, $oEncargoDiaEncontrado);
        $this->assertEquals($id, $oEncargoDiaEncontrado->getUuidItemVo());

        // Limpiar
        $this->repository->Eliminar($oEncargoDiaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $oEncargoDia = $this->repository->findById(EncargoDiaId::fromString($id_inexistente));

        $this->assertNull($oEncargoDia);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoDia = $this->factory->createSimple();
        $id = $oEncargoDia->getUuidItemVo();
        $this->repository->Guardar($oEncargoDia);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('uuid_item', $aDatos);
        $this->assertEquals($id, $aDatos['uuid_item']);

        // Limpiar
        $oEncargoDiaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoDiaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $aDatos = $this->repository->datosById(EncargoDiaId::fromString($id_inexistente));

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoDia()
    {
        // Crear y guardar instancia usando factory
        $oEncargoDia = $this->factory->createSimple();
        $id = $oEncargoDia->getUuidItemVo();
        $this->repository->Guardar($oEncargoDia);

        // Verificar que existe
        $oEncargoDiaExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoDiaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoDiaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoDiaEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoDiaEliminado);
    }

}
