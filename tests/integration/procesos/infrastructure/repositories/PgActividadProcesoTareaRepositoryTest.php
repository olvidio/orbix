<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\entity\ActividadProcesoTarea;
use Tests\myTest;
use Tests\factories\procesos\ActividadProcesoTareaFactory;

class PgActividadProcesoTareaRepositoryTest extends myTest
{
    private ActividadProcesoTareaRepositoryInterface $repository;
    private ActividadProcesoTareaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $this->factory = new ActividadProcesoTareaFactory();
    }

    public function test_guardar_nuevo_actividadProcesoTarea()
    {
        // Crear instancia usando factory
        $oActividadProcesoTarea = $this->factory->createSimple();
        $id = $oActividadProcesoTarea->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oActividadProcesoTarea);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadProcesoTareaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadProcesoTareaGuardado);
        $this->assertEquals($id, $oActividadProcesoTareaGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActividadProcesoTareaGuardado);
    }

    public function test_actualizar_actividadProcesoTarea_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadProcesoTarea = $this->factory->createSimple();
        $id = $oActividadProcesoTarea->getId_item();
        $this->repository->Guardar($oActividadProcesoTarea);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadProcesoTareaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadProcesoTareaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadProcesoTareaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadProcesoTareaActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadProcesoTareaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadProcesoTarea = $this->factory->createSimple();
        $id = $oActividadProcesoTarea->getId_item();
        $this->repository->Guardar($oActividadProcesoTarea);

        // Buscar por ID
        $oActividadProcesoTareaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadProcesoTareaEncontrado);
        $this->assertInstanceOf(ActividadProcesoTarea::class, $oActividadProcesoTareaEncontrado);
        $this->assertEquals($id, $oActividadProcesoTareaEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActividadProcesoTareaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadProcesoTarea = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadProcesoTarea);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadProcesoTarea = $this->factory->createSimple();
        $id = $oActividadProcesoTarea->getId_item();
        $this->repository->Guardar($oActividadProcesoTarea);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oActividadProcesoTareaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadProcesoTareaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadProcesoTarea()
    {
        // Crear y guardar instancia usando factory
        $oActividadProcesoTarea = $this->factory->createSimple();
        $id = $oActividadProcesoTarea->getId_item();
        $this->repository->Guardar($oActividadProcesoTarea);

        // Verificar que existe
        $oActividadProcesoTareaExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadProcesoTareaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadProcesoTareaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadProcesoTareaEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadProcesoTareaEliminado);
    }

    public function test_get_lista_fase_estado_sin_filtros()
    {
        $result = $this->repository->getListaFaseEstado(1001);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
/*
    public function test_get_fases_completadas_sin_filtros()
    {
        $result = $this->repository->getFasesCompletadas(1001);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividad_proceso_tareas_sin_filtros()
    {
        $result = $this->repository->getActividadProcesoTareas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
*/
    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
