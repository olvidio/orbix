<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\entity\ProfesorCongreso;
use Tests\myTest;
use Tests\factories\profesores\ProfesorCongresoFactory;

class PgProfesorCongresoRepositoryTest extends myTest
{
    private ProfesorCongresoRepositoryInterface $repository;
    private ProfesorCongresoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
        $this->factory = new ProfesorCongresoFactory();
    }

    public function test_guardar_nuevo_profesorCongreso()
    {
        // Crear instancia usando factory
        $oProfesorCongreso = $this->factory->createSimple();
        $id = $oProfesorCongreso->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorCongreso);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorCongresoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorCongresoGuardado);
        $this->assertEquals($id, $oProfesorCongresoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorCongresoGuardado);
    }

    public function test_actualizar_profesorCongreso_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorCongreso = $this->factory->createSimple();
        $id = $oProfesorCongreso->getId_item();
        $this->repository->Guardar($oProfesorCongreso);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorCongresoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorCongresoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorCongresoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorCongresoActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorCongresoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorCongreso = $this->factory->createSimple();
        $id = $oProfesorCongreso->getId_item();
        $this->repository->Guardar($oProfesorCongreso);

        // Buscar por ID
        $oProfesorCongresoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorCongresoEncontrado);
        $this->assertInstanceOf(ProfesorCongreso::class, $oProfesorCongresoEncontrado);
        $this->assertEquals($id, $oProfesorCongresoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorCongresoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorCongreso = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorCongreso);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorCongreso = $this->factory->createSimple();
        $id = $oProfesorCongreso->getId_item();
        $this->repository->Guardar($oProfesorCongreso);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorCongresoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorCongresoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorCongreso()
    {
        // Crear y guardar instancia usando factory
        $oProfesorCongreso = $this->factory->createSimple();
        $id = $oProfesorCongreso->getId_item();
        $this->repository->Guardar($oProfesorCongreso);

        // Verificar que existe
        $oProfesorCongresoExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorCongresoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorCongresoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorCongresoEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorCongresoEliminado);
    }

    public function test_get_profesor_congresos_sin_filtros()
    {
        $result = $this->repository->getProfesorCongresos();
        
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
