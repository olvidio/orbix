<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\TareaProceso;
use Tests\myTest;
use Tests\factories\procesos\TareaProcesoFactory;

class PgTareaProcesoRepositoryTest extends myTest
{
    private TareaProcesoRepositoryInterface $repository;
    private TareaProcesoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->factory = new TareaProcesoFactory();
    }

    public function test_guardar_nuevo_tareaProceso()
    {
        // Crear instancia usando factory
        $oTareaProceso = $this->factory->createSimple();
        $id = $oTareaProceso->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oTareaProceso);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTareaProcesoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTareaProcesoGuardado);
        $this->assertEquals($id, $oTareaProcesoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTareaProcesoGuardado);
    }

    public function test_actualizar_tareaProceso_existente()
    {
        // Crear y guardar instancia usando factory
        $oTareaProceso = $this->factory->createSimple();
        $id = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);

        // Crear otra instancia con datos diferentes para actualizar
        $oTareaProcesoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTareaProcesoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTareaProcesoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTareaProcesoActualizado);

        // Limpiar
        $this->repository->Eliminar($oTareaProcesoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTareaProceso = $this->factory->createSimple();
        $id = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);

        // Buscar por ID
        $oTareaProcesoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTareaProcesoEncontrado);
        $this->assertInstanceOf(TareaProceso::class, $oTareaProcesoEncontrado);
        $this->assertEquals($id, $oTareaProcesoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTareaProcesoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTareaProceso = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTareaProceso);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTareaProceso = $this->factory->createSimple();
        $id = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oTareaProcesoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTareaProcesoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tareaProceso()
    {
        // Crear y guardar instancia usando factory
        $oTareaProceso = $this->factory->createSimple();
        $id = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);

        // Verificar que existe
        $oTareaProcesoExiste = $this->repository->findById($id);
        $this->assertNotNull($oTareaProcesoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTareaProcesoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTareaProcesoEliminado = $this->repository->findById($id);
        $this->assertNull($oTareaProcesoEliminado);
    }

    public function test_get_array_fases_dependientes_sin_filtros()
    {
        $result = $this->repository->getArrayFasesDependientes(3);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_fases_proceso_sin_filtros()
    {
        $result = $this->repository->getFasesProceso(3);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_fase_independiente_sin_filtros()
    {
        $result = $this->repository->getFaseIndependiente(3);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_tareas_proceso_sin_filtros()
    {
        $result = $this->repository->getTareasProceso();
        
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
