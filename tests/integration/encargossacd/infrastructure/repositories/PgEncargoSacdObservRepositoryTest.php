<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdObserv;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoSacdObservFactory;

class PgEncargoSacdObservRepositoryTest extends myTest
{
    private EncargoSacdObservRepositoryInterface $repository;
    private EncargoSacdObservFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoSacdObservRepositoryInterface::class);
        $this->factory = new EncargoSacdObservFactory();
    }

    public function test_guardar_nuevo_encargoSacdObserv()
    {
        // Crear instancia usando factory
        $oEncargoSacdObserv = $this->factory->createSimple();
        $id = $oEncargoSacdObserv->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oEncargoSacdObserv);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoSacdObservGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdObservGuardado);
        $this->assertEquals($id, $oEncargoSacdObservGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdObservGuardado);
    }

    public function test_actualizar_encargoSacdObserv_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdObserv = $this->factory->createSimple();
        $id = $oEncargoSacdObserv->getId_item();
        $this->repository->Guardar($oEncargoSacdObserv);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoSacdObservUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoSacdObservUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoSacdObservActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdObservActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdObservActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdObserv = $this->factory->createSimple();
        $id = $oEncargoSacdObserv->getId_item();
        $this->repository->Guardar($oEncargoSacdObserv);

        // Buscar por ID
        $oEncargoSacdObservEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdObservEncontrado);
        $this->assertInstanceOf(EncargoSacdObserv::class, $oEncargoSacdObservEncontrado);
        $this->assertEquals($id, $oEncargoSacdObservEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdObservEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoSacdObserv = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoSacdObserv);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdObserv = $this->factory->createSimple();
        $id = $oEncargoSacdObserv->getId_item();
        $this->repository->Guardar($oEncargoSacdObserv);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oEncargoSacdObservParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoSacdObservParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoSacdObserv()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdObserv = $this->factory->createSimple();
        $id = $oEncargoSacdObserv->getId_item();
        $this->repository->Guardar($oEncargoSacdObserv);

        // Verificar que existe
        $oEncargoSacdObservExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdObservExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoSacdObservExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoSacdObservEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoSacdObservEliminado);
    }

    public function test_get_encargos_sacd_observs_sin_filtros()
    {
        $result = $this->repository->getEncargosSacdObservs();
        
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
