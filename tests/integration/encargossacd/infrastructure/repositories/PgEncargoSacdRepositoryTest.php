<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoSacdFactory;

class PgEncargoSacdRepositoryTest extends myTest
{
    private EncargoSacdRepositoryInterface $repository;
    private EncargoSacdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $this->factory = new EncargoSacdFactory();
    }

    public function test_guardar_nuevo_encargoSacd()
    {
        // Crear instancia usando factory
        $oEncargoSacd = $this->factory->createSimple();
        $id = $oEncargoSacd->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oEncargoSacd);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoSacdGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdGuardado);
        $this->assertEquals($id, $oEncargoSacdGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdGuardado);
    }

    public function test_actualizar_encargoSacd_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacd = $this->factory->createSimple();
        $id = $oEncargoSacd->getId_item();
        $this->repository->Guardar($oEncargoSacd);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoSacdUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoSacdUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoSacdActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacd = $this->factory->createSimple();
        $id = $oEncargoSacd->getId_item();
        $this->repository->Guardar($oEncargoSacd);

        // Buscar por ID
        $oEncargoSacdEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdEncontrado);
        $this->assertInstanceOf(EncargoSacd::class, $oEncargoSacdEncontrado);
        $this->assertEquals($id, $oEncargoSacdEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoSacd = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoSacd);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacd = $this->factory->createSimple();
        $id = $oEncargoSacd->getId_item();
        $this->repository->Guardar($oEncargoSacd);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oEncargoSacdParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoSacdParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoSacd()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacd = $this->factory->createSimple();
        $id = $oEncargoSacd->getId_item();
        $this->repository->Guardar($oEncargoSacd);

        // Verificar que existe
        $oEncargoSacdExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoSacdExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoSacdEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoSacdEliminado);
    }

    public function test_get_encargos_sacd_sin_filtros()
    {
        $result = $this->repository->getEncargosSacd();
        
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
