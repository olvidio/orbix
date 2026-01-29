<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;
use Tests\factories\ubis\DelegacionFactory;

class PgDelegacionRepositoryTest extends myTest
{
    private DelegacionRepositoryInterface $repository;
    private DelegacionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $this->factory = new DelegacionFactory();
    }

    public function test_guardar_nuevo_delegacion()
    {
        // Crear instancia usando factory
        $oDelegacion = $this->factory->createSimple();
        $id = $oDelegacion->getId_dl();

        // Guardar
        $result = $this->repository->Guardar($oDelegacion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oDelegacionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oDelegacionGuardado);
        $this->assertEquals($id, $oDelegacionGuardado->getId_dl());

        // Limpiar
        $this->repository->Eliminar($oDelegacionGuardado);
    }

    public function test_actualizar_delegacion_existente()
    {
        // Crear y guardar instancia usando factory
        $oDelegacion = $this->factory->createSimple();
        $id = $oDelegacion->getId_dl();
        $this->repository->Guardar($oDelegacion);

        // Crear otra instancia con datos diferentes para actualizar
        $oDelegacionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oDelegacionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oDelegacionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oDelegacionActualizado);

        // Limpiar
        $this->repository->Eliminar($oDelegacionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDelegacion = $this->factory->createSimple();
        $id = $oDelegacion->getId_dl();
        $this->repository->Guardar($oDelegacion);

        // Buscar por ID
        $oDelegacionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oDelegacionEncontrado);
        $this->assertInstanceOf(Delegacion::class, $oDelegacionEncontrado);
        $this->assertEquals($id, $oDelegacionEncontrado->getId_dl());

        // Limpiar
        $this->repository->Eliminar($oDelegacionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oDelegacion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oDelegacion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDelegacion = $this->factory->createSimple();
        $id = $oDelegacion->getId_dl();
        $this->repository->Guardar($oDelegacion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_dl', $aDatos);
        $this->assertEquals($id, $aDatos['id_dl']);

        // Limpiar
        $oDelegacionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oDelegacionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_delegacion()
    {
        // Crear y guardar instancia usando factory
        $oDelegacion = $this->factory->createSimple();
        $id = $oDelegacion->getId_dl();
        $this->repository->Guardar($oDelegacion);

        // Verificar que existe
        $oDelegacionExiste = $this->repository->findById($id);
        $this->assertNotNull($oDelegacionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oDelegacionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oDelegacionEliminado = $this->repository->findById($id);
        $this->assertNull($oDelegacionEliminado);
    }

    /*
    public function test_get_array_id_schema_region_stgr_sin_filtros()
    {
        $result = $this->repository->getArrayIdSchemaRegionStgr();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_schemas_region_stgr_sin_filtros()
    {
        $result = $this->repository->getArraySchemasRegionStgr();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    public function test_get_delegaciones_sin_filtros()
    {
        $result = $this->repository->getDelegaciones();
        
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
