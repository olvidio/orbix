<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\entity\ActividadTarea;
use Tests\myTest;
use Tests\factories\procesos\ActividadTareaFactory;

class PgActividadTareaRepositoryTest extends myTest
{
    private ActividadTareaRepositoryInterface $repository;
    private ActividadTareaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $this->factory = new ActividadTareaFactory();
    }

    public function test_guardar_nuevo_actividadTarea()
    {
        // Crear instancia usando factory
        $oActividadTarea = $this->factory->createSimple();
        $id = $oActividadTarea->getId_tarea();

        // Guardar
        $result = $this->repository->Guardar($oActividadTarea);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadTareaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadTareaGuardado);
        $this->assertEquals($id, $oActividadTareaGuardado->getId_tarea());

        // Limpiar
        $this->repository->Eliminar($oActividadTareaGuardado);
    }

    public function test_actualizar_actividadTarea_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadTarea = $this->factory->createSimple();
        $id = $oActividadTarea->getId_tarea();
        $this->repository->Guardar($oActividadTarea);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadTareaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadTareaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadTareaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadTareaActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadTareaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadTarea = $this->factory->createSimple();
        $id = $oActividadTarea->getId_tarea();
        $this->repository->Guardar($oActividadTarea);

        // Buscar por ID
        $oActividadTareaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadTareaEncontrado);
        $this->assertInstanceOf(ActividadTarea::class, $oActividadTareaEncontrado);
        $this->assertEquals($id, $oActividadTareaEncontrado->getId_tarea());

        // Limpiar
        $this->repository->Eliminar($oActividadTareaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadTarea = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadTarea);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadTarea = $this->factory->createSimple();
        $id = $oActividadTarea->getId_tarea();
        $this->repository->Guardar($oActividadTarea);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tarea', $aDatos);
        $this->assertEquals($id, $aDatos['id_tarea']);

        // Limpiar
        $oActividadTareaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadTareaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadTarea()
    {
        // Crear y guardar instancia usando factory
        $oActividadTarea = $this->factory->createSimple();
        $id = $oActividadTarea->getId_tarea();
        $this->repository->Guardar($oActividadTarea);

        // Verificar que existe
        $oActividadTareaExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadTareaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadTareaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadTareaEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadTareaEliminado);
    }

    /*
    public function test_get_array_actividad_tareas_sin_filtros()
    {
        $result = $this->repository->getArrayActividadTareas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividad_tareas_sin_filtros()
    {
        $result = $this->repository->getActividadTareas();
        
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
