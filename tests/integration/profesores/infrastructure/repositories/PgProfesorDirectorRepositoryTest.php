<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\entity\ProfesorDirector;
use Tests\myTest;
use Tests\factories\profesores\ProfesorDirectorFactory;

class PgProfesorDirectorRepositoryTest extends myTest
{
    private ProfesorDirectorRepositoryInterface $repository;
    private ProfesorDirectorFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
        $this->factory = new ProfesorDirectorFactory();
    }

    public function test_guardar_nuevo_profesorDirector()
    {
        // Crear instancia usando factory
        $oProfesorDirector = $this->factory->createSimple();
        $id = $oProfesorDirector->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorDirector);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorDirectorGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDirectorGuardado);
        $this->assertEquals($id, $oProfesorDirectorGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorDirectorGuardado);
    }

    public function test_actualizar_profesorDirector_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDirector = $this->factory->createSimple();
        $id = $oProfesorDirector->getId_item();
        $this->repository->Guardar($oProfesorDirector);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorDirectorUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorDirectorUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorDirectorActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDirectorActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorDirectorActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDirector = $this->factory->createSimple();
        $id = $oProfesorDirector->getId_item();
        $this->repository->Guardar($oProfesorDirector);

        // Buscar por ID
        $oProfesorDirectorEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDirectorEncontrado);
        $this->assertInstanceOf(ProfesorDirector::class, $oProfesorDirectorEncontrado);
        $this->assertEquals($id, $oProfesorDirectorEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorDirectorEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorDirector = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorDirector);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDirector = $this->factory->createSimple();
        $id = $oProfesorDirector->getId_item();
        $this->repository->Guardar($oProfesorDirector);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorDirectorParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorDirectorParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorDirector()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDirector = $this->factory->createSimple();
        $id = $oProfesorDirector->getId_item();
        $this->repository->Guardar($oProfesorDirector);

        // Verificar que existe
        $oProfesorDirectorExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDirectorExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorDirectorExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorDirectorEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorDirectorEliminado);
    }

    public function test_get_profesores_directores_sin_filtros()
    {
        $result = $this->repository->getProfesoresDirectores();
        
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
