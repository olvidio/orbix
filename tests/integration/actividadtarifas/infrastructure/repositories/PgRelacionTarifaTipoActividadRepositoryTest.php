<?php

namespace Tests\integration\actividadtarifas\infrastructure\repositories;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use Tests\myTest;
use Tests\factories\actividadtarifas\RelacionTarifaTipoActividadFactory;

class PgRelacionTarifaTipoActividadRepositoryTest extends myTest
{
    private RelacionTarifaTipoActividadRepositoryInterface $repository;
    private RelacionTarifaTipoActividadFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $this->factory = new RelacionTarifaTipoActividadFactory();
    }

    public function test_guardar_nuevo_relacionTarifaTipoActividad()
    {
        // Crear instancia usando factory
        $oRelacionTarifaTipoActividad = $this->factory->createSimple();
        $id = $oRelacionTarifaTipoActividad->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oRelacionTarifaTipoActividad);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oRelacionTarifaTipoActividadGuardado = $this->repository->findById($id);
        $this->assertNotNull($oRelacionTarifaTipoActividadGuardado);
        $this->assertEquals($id, $oRelacionTarifaTipoActividadGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oRelacionTarifaTipoActividadGuardado);
    }

    public function test_actualizar_relacionTarifaTipoActividad_existente()
    {
        // Crear y guardar instancia usando factory
        $oRelacionTarifaTipoActividad = $this->factory->createSimple();
        $id = $oRelacionTarifaTipoActividad->getId_item();
        $this->repository->Guardar($oRelacionTarifaTipoActividad);

        // Crear otra instancia con datos diferentes para actualizar
        $oRelacionTarifaTipoActividadUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oRelacionTarifaTipoActividadUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oRelacionTarifaTipoActividadActualizado = $this->repository->findById($id);
        $this->assertNotNull($oRelacionTarifaTipoActividadActualizado);

        // Limpiar
        $this->repository->Eliminar($oRelacionTarifaTipoActividadActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRelacionTarifaTipoActividad = $this->factory->createSimple();
        $id = $oRelacionTarifaTipoActividad->getId_item();
        $this->repository->Guardar($oRelacionTarifaTipoActividad);

        // Buscar por ID
        $oRelacionTarifaTipoActividadEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oRelacionTarifaTipoActividadEncontrado);
        $this->assertInstanceOf(RelacionTarifaTipoActividad::class, $oRelacionTarifaTipoActividadEncontrado);
        $this->assertEquals($id, $oRelacionTarifaTipoActividadEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oRelacionTarifaTipoActividadEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oRelacionTarifaTipoActividad = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oRelacionTarifaTipoActividad);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRelacionTarifaTipoActividad = $this->factory->createSimple();
        $id = $oRelacionTarifaTipoActividad->getId_item();
        $this->repository->Guardar($oRelacionTarifaTipoActividad);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oRelacionTarifaTipoActividadParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oRelacionTarifaTipoActividadParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_relacionTarifaTipoActividad()
    {
        // Crear y guardar instancia usando factory
        $oRelacionTarifaTipoActividad = $this->factory->createSimple();
        $id = $oRelacionTarifaTipoActividad->getId_item();
        $this->repository->Guardar($oRelacionTarifaTipoActividad);

        // Verificar que existe
        $oRelacionTarifaTipoActividadExiste = $this->repository->findById($id);
        $this->assertNotNull($oRelacionTarifaTipoActividadExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oRelacionTarifaTipoActividadExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oRelacionTarifaTipoActividadEliminado = $this->repository->findById($id);
        $this->assertNull($oRelacionTarifaTipoActividadEliminado);
    }

    public function test_get_relacion_tarifas_tipo_activides_sin_filtros()
    {
        $result = $this->repository->getRelacionTarifasTipoActivides();
        
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
