<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use Tests\factories\ubis\TelecoUbiFactory;
use Tests\myTest;

class PgTelecoCentroExRepositoryTest extends myTest
{
    private TelecoUbiRepositoryInterface $repository;
    private TelecoUbiFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
        $this->factory = new TelecoUbiFactory();
    }

    public function test_guardar_nuevo_telecoUbi()
    {
        // Crear instancia usando factory
        $oTelecoCdc = $this->factory->createSimple();
        $id = $oTelecoCdc->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oTelecoCdc);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTelecoCdcGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoCdcGuardado);
        $this->assertEquals($id, $oTelecoCdcGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTelecoCdcGuardado);
    }

    public function test_actualizar_telecoUbi_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoCdc = $this->factory->createSimple();
        $id = $oTelecoCdc->getId_item();
        $this->repository->Guardar($oTelecoCdc);

        // Crear otra instancia con datos diferentes para actualizar
        $oTelecoCdcUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTelecoCdcUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTelecoCdcActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoCdcActualizado);

        // Limpiar
        $this->repository->Eliminar($oTelecoCdcActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoCdc = $this->factory->createSimple();
        $id = $oTelecoCdc->getId_item();
        $this->repository->Guardar($oTelecoCdc);

        // Buscar por ID
        $oTelecoCdcEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoCdcEncontrado);
        $this->assertInstanceOf(TelecoUbi::class, $oTelecoCdcEncontrado);
        $this->assertEquals($id, $oTelecoCdcEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTelecoCdcEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTelecoCdc = $this->repository->findById($id_inexistente);

        $this->assertNull($oTelecoCdc);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoCdc = $this->factory->createSimple();
        $id = $oTelecoCdc->getId_item();
        $this->repository->Guardar($oTelecoCdc);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oTelecoCdcParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTelecoCdcParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_telecoUbi()
    {
        // Crear y guardar instancia usando factory
        $oTelecoCdc = $this->factory->createSimple();
        $id = $oTelecoCdc->getId_item();
        $this->repository->Guardar($oTelecoCdc);

        // Verificar que existe
        $oTelecoCdcExiste = $this->repository->findById($id);
        $this->assertNotNull($oTelecoCdcExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTelecoCdcExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTelecoCdcEliminado = $this->repository->findById($id);
        $this->assertNull($oTelecoCdcEliminado);
    }

    public function test_get_telecos_sin_filtros()
    {
        $result = $this->repository->getTelecos();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
