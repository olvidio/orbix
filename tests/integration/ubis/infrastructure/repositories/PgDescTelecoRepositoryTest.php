<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\entity\DescTeleco;
use Tests\myTest;
use Tests\factories\ubis\DescTelecoFactory;

class PgDescTelecoRepositoryTest extends myTest
{
    private DescTelecoRepositoryInterface $repository;
    private DescTelecoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
        $this->factory = new DescTelecoFactory();
    }

    public function test_guardar_nuevo_descTeleco()
    {
        // Crear instancia usando factory
        $oDescTeleco = $this->factory->createSimple();
        $id = $oDescTeleco->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oDescTeleco);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oDescTelecoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oDescTelecoGuardado);
        $this->assertEquals($id, $oDescTelecoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oDescTelecoGuardado);
    }

    public function test_actualizar_descTeleco_existente()
    {
        // Crear y guardar instancia usando factory
        $oDescTeleco = $this->factory->createSimple();
        $id = $oDescTeleco->getId_item();
        $this->repository->Guardar($oDescTeleco);

        // Crear otra instancia con datos diferentes para actualizar
        $oDescTelecoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oDescTelecoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oDescTelecoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oDescTelecoActualizado);

        // Limpiar
        $this->repository->Eliminar($oDescTelecoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDescTeleco = $this->factory->createSimple();
        $id = $oDescTeleco->getId_item();
        $this->repository->Guardar($oDescTeleco);

        // Buscar por ID
        $oDescTelecoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oDescTelecoEncontrado);
        $this->assertInstanceOf(DescTeleco::class, $oDescTelecoEncontrado);
        $this->assertEquals($id, $oDescTelecoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oDescTelecoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oDescTeleco = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oDescTeleco);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDescTeleco = $this->factory->createSimple();
        $id = $oDescTeleco->getId_item();
        $this->repository->Guardar($oDescTeleco);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oDescTelecoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oDescTelecoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_descTeleco()
    {
        // Crear y guardar instancia usando factory
        $oDescTeleco = $this->factory->createSimple();
        $id = $oDescTeleco->getId_item();
        $this->repository->Guardar($oDescTeleco);

        // Verificar que existe
        $oDescTelecoExiste = $this->repository->findById($id);
        $this->assertNotNull($oDescTelecoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oDescTelecoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oDescTelecoEliminado = $this->repository->findById($id);
        $this->assertNull($oDescTelecoEliminado);
    }

    /*
    public function test_get_array_desc_teleco_personas_sin_filtros()
    {
        $result = $this->repository->getArrayDescTelecoPersonas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_desc_teleco_ubis_sin_filtros()
    {
        $result = $this->repository->getArrayDescTelecoUbis();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    public function test_get_descs_teleco_sin_filtros()
    {
        $result = $this->repository->getDescsTeleco();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
