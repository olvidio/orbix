<?php

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;
use Tests\myTest;
use Tests\factories\zonassacd\ZonaSacdFactory;

class PgZonaSacdRepositoryTest extends myTest
{
    private ZonaSacdRepositoryInterface $repository;
    private ZonaSacdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $this->factory = new ZonaSacdFactory();
    }

    public function test_guardar_nuevo_zonaSacd()
    {
        // Crear instancia usando factory
        $oZonaSacd = $this->factory->createSimple();
        $id = $oZonaSacd->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oZonaSacd);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oZonaSacdGuardado = $this->repository->findById($id);
        $this->assertNotNull($oZonaSacdGuardado);
        $this->assertEquals($id, $oZonaSacdGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oZonaSacdGuardado);
    }

    public function test_actualizar_zonaSacd_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaSacd = $this->factory->createSimple();
        $id = $oZonaSacd->getId_item();
        $this->repository->Guardar($oZonaSacd);

        // Crear otra instancia con datos diferentes para actualizar
        $oZonaSacdUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oZonaSacdUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oZonaSacdActualizado = $this->repository->findById($id);
        $this->assertNotNull($oZonaSacdActualizado);

        // Limpiar
        $this->repository->Eliminar($oZonaSacdActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaSacd = $this->factory->createSimple();
        $id = $oZonaSacd->getId_item();
        $this->repository->Guardar($oZonaSacd);

        // Buscar por ID
        $oZonaSacdEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oZonaSacdEncontrado);
        $this->assertInstanceOf(ZonaSacd::class, $oZonaSacdEncontrado);
        $this->assertEquals($id, $oZonaSacdEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oZonaSacdEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oZonaSacd = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oZonaSacd);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaSacd = $this->factory->createSimple();
        $id = $oZonaSacd->getId_item();
        $this->repository->Guardar($oZonaSacd);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oZonaSacdParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oZonaSacdParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_zonaSacd()
    {
        // Crear y guardar instancia usando factory
        $oZonaSacd = $this->factory->createSimple();
        $id = $oZonaSacd->getId_item();
        $this->repository->Guardar($oZonaSacd);

        // Verificar que existe
        $oZonaSacdExiste = $this->repository->findById($id);
        $this->assertNotNull($oZonaSacdExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oZonaSacdExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oZonaSacdEliminado = $this->repository->findById($id);
        $this->assertNull($oZonaSacdEliminado);
    }

    public function test_get_id_sacds_de_zona_sin_filtros()
    {
        // Necesito un id de zona para probar
        $id_zona_valida = 1;
        $result = $this->repository->getIdSacdsDeZona($id_zona_valida);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_zonas_sacds_sin_filtros()
    {
        $result = $this->repository->getZonasSacds();
        
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
