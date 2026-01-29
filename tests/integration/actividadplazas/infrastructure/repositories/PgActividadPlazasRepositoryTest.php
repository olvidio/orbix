<?php

namespace Tests\integration\actividadplazas\infrastructure\repositories;

use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use Tests\myTest;
use Tests\factories\actividadplazas\ActividadPlazasFactory;

class PgActividadPlazasRepositoryTest extends myTest
{
    private ActividadPlazasRepositoryInterface $repository;
    private ActividadPlazasFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $this->factory = new ActividadPlazasFactory();
    }

    public function test_guardar_nuevo_actividadPlazas()
    {
        // Crear instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();

        // Guardar
        $result = $this->repository->Guardar($oActividadPlazas);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadPlazasGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadPlazasGuardado);
        $this->assertEquals($id, $oActividadPlazasGuardado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasGuardado);
    }

    public function test_actualizar_actividadPlazas_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadPlazasUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadPlazasUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadPlazasActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadPlazasActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Buscar por ID
        $oActividadPlazasEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadPlazasEncontrado);
        $this->assertInstanceOf(ActividadPlazas::class, $oActividadPlazasEncontrado);
        $this->assertEquals($id, $oActividadPlazasEncontrado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadPlazas = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadPlazas);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_activ']);

        // Limpiar
        $oActividadPlazasParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadPlazasParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadPlazas()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Verificar que existe
        $oActividadPlazasExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadPlazasExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadPlazasExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadPlazasEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadPlazasEliminado);
    }

    public function test_get_actividades_plazas_sin_filtros()
    {
        $result = $this->repository->getActividadesPlazas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
