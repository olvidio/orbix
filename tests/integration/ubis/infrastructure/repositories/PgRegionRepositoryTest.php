<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\entity\Region;
use Tests\myTest;
use Tests\factories\ubis\RegionFactory;

class PgRegionRepositoryTest extends myTest
{
    private RegionRepositoryInterface $repository;
    private RegionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RegionRepositoryInterface::class);
        $this->factory = new RegionFactory();
    }

    public function test_guardar_nuevo_region()
    {
        // Crear instancia usando factory
        $oRegion = $this->factory->createSimple();
        $id = $oRegion->getId_region();

        // Guardar
        $result = $this->repository->Guardar($oRegion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oRegionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oRegionGuardado);
        $this->assertEquals($id, $oRegionGuardado->getId_region());

        // Limpiar
        $this->repository->Eliminar($oRegionGuardado);
    }

    public function test_actualizar_region_existente()
    {
        // Crear y guardar instancia usando factory
        $oRegion = $this->factory->createSimple();
        $id = $oRegion->getId_region();
        $this->repository->Guardar($oRegion);

        // Crear otra instancia con datos diferentes para actualizar
        $oRegionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oRegionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oRegionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oRegionActualizado);

        // Limpiar
        $this->repository->Eliminar($oRegionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRegion = $this->factory->createSimple();
        $id = $oRegion->getId_region();
        $this->repository->Guardar($oRegion);

        // Buscar por ID
        $oRegionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oRegionEncontrado);
        $this->assertInstanceOf(Region::class, $oRegionEncontrado);
        $this->assertEquals($id, $oRegionEncontrado->getId_region());

        // Limpiar
        $this->repository->Eliminar($oRegionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oRegion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oRegion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRegion = $this->factory->createSimple();
        $id = $oRegion->getId_region();
        $this->repository->Guardar($oRegion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_region', $aDatos);
        $this->assertEquals($id, $aDatos['id_region']);

        // Limpiar
        $oRegionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oRegionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_region()
    {
        // Crear y guardar instancia usando factory
        $oRegion = $this->factory->createSimple();
        $id = $oRegion->getId_region();
        $this->repository->Guardar($oRegion);

        // Verificar que existe
        $oRegionExiste = $this->repository->findById($id);
        $this->assertNotNull($oRegionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oRegionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oRegionEliminado = $this->repository->findById($id);
        $this->assertNull($oRegionEliminado);
    }

    public function test_get_regiones_sin_filtros()
    {
        $result = $this->repository->getRegiones();
        
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
