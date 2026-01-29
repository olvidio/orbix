<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use Tests\myTest;
use Tests\factories\procesos\ActividadFaseFactory;

class PgActividadFaseRepositoryTest extends myTest
{
    private ActividadFaseRepositoryInterface $repository;
    private ActividadFaseFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $this->factory = new ActividadFaseFactory();
    }

    public function test_guardar_nuevo_actividadFase()
    {
        // Crear instancia usando factory
        $oActividadFase = $this->factory->createSimple();
        $id = $oActividadFase->getId_fase();

        // Guardar
        $result = $this->repository->Guardar($oActividadFase);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadFaseGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadFaseGuardado);
        $this->assertEquals($id, $oActividadFaseGuardado->getId_fase());

        // Limpiar
        $this->repository->Eliminar($oActividadFaseGuardado);
    }

    public function test_actualizar_actividadFase_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadFase = $this->factory->createSimple();
        $id = $oActividadFase->getId_fase();
        $this->repository->Guardar($oActividadFase);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadFaseUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadFaseUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadFaseActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadFaseActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadFaseActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadFase = $this->factory->createSimple();
        $id = $oActividadFase->getId_fase();
        $this->repository->Guardar($oActividadFase);

        // Buscar por ID
        $oActividadFaseEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadFaseEncontrado);
        $this->assertInstanceOf(ActividadFase::class, $oActividadFaseEncontrado);
        $this->assertEquals($id, $oActividadFaseEncontrado->getId_fase());

        // Limpiar
        $this->repository->Eliminar($oActividadFaseEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadFase = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadFase);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadFase = $this->factory->createSimple();
        $id = $oActividadFase->getId_fase();
        $this->repository->Guardar($oActividadFase);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_fase', $aDatos);
        $this->assertEquals($id, $aDatos['id_fase']);

        // Limpiar
        $oActividadFaseParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadFaseParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadFase()
    {
        // Crear y guardar instancia usando factory
        $oActividadFase = $this->factory->createSimple();
        $id = $oActividadFase->getId_fase();
        $this->repository->Guardar($oActividadFase);

        // Verificar que existe
        $oActividadFaseExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadFaseExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadFaseExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadFaseEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadFaseEliminado);
    }

    public function test_get_todas_actividad_fases_sin_filtros()
    {
        $result = $this->repository->getTodasActividadFases([4,23]);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_actividad_fases_todas_sin_filtros()
    {
        $result = $this->repository->getArrayActividadFasesTodas([4,23]);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_fases_procesos_sin_filtros()
    {
        $result = $this->repository->getArrayFasesProcesos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_actividad_fases_sin_filtros()
    {
        $result = $this->repository->getArrayActividadFases();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividad_fases_sin_filtros()
    {
        $result = $this->repository->getActividadFases();
        
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
