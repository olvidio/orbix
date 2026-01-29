<?php

namespace Tests\integration\misas\infrastructure\repositories;

use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;
use Tests\myTest;
use Tests\factories\misas\EncargoCtrFactory;

class PgEncargoCtrRepositoryTest extends myTest
{
    private EncargoCtrRepositoryInterface $repository;
    private EncargoCtrFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
        $this->factory = new EncargoCtrFactory();
    }

    public function test_guardar_nuevo_encargoCtr()
    {
        // Crear instancia usando factory
        $oEncargoCtr = $this->factory->createSimple();
        $id = $oEncargoCtr->getUuidItemVo();

        // Guardar
        $result = $this->repository->Guardar($oEncargoCtr);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoCtrGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoCtrGuardado);
        $this->assertEquals($id, $oEncargoCtrGuardado->getUuidItemVo());

        // Limpiar
        $this->repository->Eliminar($oEncargoCtrGuardado);
    }

    public function test_actualizar_encargoCtr_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoCtr = $this->factory->createSimple();
        $id = $oEncargoCtr->getUuidItemVo();
        $this->repository->Guardar($oEncargoCtr);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoCtrUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoCtrUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoCtrActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoCtrActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoCtrActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoCtr = $this->factory->createSimple();
        $id = $oEncargoCtr->getUuidItemVo();
        $this->repository->Guardar($oEncargoCtr);

        // Buscar por ID
        $oEncargoCtrEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoCtrEncontrado);
        $this->assertInstanceOf(EncargoCtr::class, $oEncargoCtrEncontrado);
        $this->assertEquals($id, $oEncargoCtrEncontrado->getUuidItemVo());

        // Limpiar
        $this->repository->Eliminar($oEncargoCtrEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $oEncargoCtr = $this->repository->findById(EncargoCtrId::fromString($id_inexistente));
        
        $this->assertNull($oEncargoCtr);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoCtr = $this->factory->createSimple();
        $id = $oEncargoCtr->getUuidItemVo();
        $this->repository->Guardar($oEncargoCtr);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('uuid_item', $aDatos);
        $this->assertEquals($id, $aDatos['uuid_item']);

        // Limpiar
        $oEncargoCtrParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoCtrParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $aDatos = $this->repository->datosById(EncargoCtrId::fromString($id_inexistente));
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoCtr()
    {
        // Crear y guardar instancia usando factory
        $oEncargoCtr = $this->factory->createSimple();
        $id = $oEncargoCtr->getUuidItemVo();
        $this->repository->Guardar($oEncargoCtr);

        // Verificar que existe
        $oEncargoCtrExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoCtrExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoCtrExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoCtrEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoCtrEliminado);
    }


}
