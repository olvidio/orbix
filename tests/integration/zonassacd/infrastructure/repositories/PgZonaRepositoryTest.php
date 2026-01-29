<?php

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use Tests\myTest;
use Tests\factories\zonassacd\ZonaFactory;

class PgZonaRepositoryTest extends myTest
{
    private ZonaRepositoryInterface $repository;
    private ZonaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $this->factory = new ZonaFactory();
    }

    public function test_guardar_nuevo_zona()
    {
        // Crear instancia usando factory
        $oZona = $this->factory->createSimple();
        $id = $oZona->getId_zona();

        // Guardar
        $result = $this->repository->Guardar($oZona);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oZonaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oZonaGuardado);
        $this->assertEquals($id, $oZonaGuardado->getId_zona());

        // Limpiar
        $this->repository->Eliminar($oZonaGuardado);
    }

    public function test_actualizar_zona_existente()
    {
        // Crear y guardar instancia usando factory
        $oZona = $this->factory->createSimple();
        $id = $oZona->getId_zona();
        $this->repository->Guardar($oZona);

        // Crear otra instancia con datos diferentes para actualizar
        $oZonaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oZonaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oZonaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oZonaActualizado);

        // Limpiar
        $this->repository->Eliminar($oZonaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZona = $this->factory->createSimple();
        $id = $oZona->getId_zona();
        $this->repository->Guardar($oZona);

        // Buscar por ID
        $oZonaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oZonaEncontrado);
        $this->assertInstanceOf(Zona::class, $oZonaEncontrado);
        $this->assertEquals($id, $oZonaEncontrado->getId_zona());

        // Limpiar
        $this->repository->Eliminar($oZonaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oZona = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oZona);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZona = $this->factory->createSimple();
        $id = $oZona->getId_zona();
        $this->repository->Guardar($oZona);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_zona', $aDatos);
        $this->assertEquals($id, $aDatos['id_zona']);

        // Limpiar
        $oZonaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oZonaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_zona()
    {
        // Crear y guardar instancia usando factory
        $oZona = $this->factory->createSimple();
        $id = $oZona->getId_zona();
        $this->repository->Guardar($oZona);

        // Verificar que existe
        $oZonaExiste = $this->repository->findById($id);
        $this->assertNotNull($oZonaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oZonaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oZonaEliminado = $this->repository->findById($id);
        $this->assertNull($oZonaEliminado);
    }

    public function test_get_array_zonas_sin_filtros()
    {
        $result = $this->repository->getArrayZonas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_zonas_sin_filtros()
    {
        $result = $this->repository->getZonas();
        
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
