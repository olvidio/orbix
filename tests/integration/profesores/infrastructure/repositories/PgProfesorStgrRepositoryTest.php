<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorStgr;
use Tests\myTest;
use Tests\factories\profesores\ProfesorStgrFactory;

class PgProfesorStgrRepositoryTest extends myTest
{
    private ProfesorStgrRepositoryInterface $repository;
    private ProfesorStgrFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $this->factory = new ProfesorStgrFactory();
    }

    public function test_guardar_nuevo_profesorStgr()
    {
        // Crear instancia usando factory
        $oProfesorStgr = $this->factory->createSimple();
        $id = $oProfesorStgr->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorStgr);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorStgrGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorStgrGuardado);
        $this->assertEquals($id, $oProfesorStgrGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorStgrGuardado);
    }

    public function test_actualizar_profesorStgr_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorStgr = $this->factory->createSimple();
        $id = $oProfesorStgr->getId_item();
        $this->repository->Guardar($oProfesorStgr);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorStgrUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorStgrUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorStgrActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorStgrActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorStgrActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorStgr = $this->factory->createSimple();
        $id = $oProfesorStgr->getId_item();
        $this->repository->Guardar($oProfesorStgr);

        // Buscar por ID
        $oProfesorStgrEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorStgrEncontrado);
        $this->assertInstanceOf(ProfesorStgr::class, $oProfesorStgrEncontrado);
        $this->assertEquals($id, $oProfesorStgrEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorStgrEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorStgr = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorStgr);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorStgr = $this->factory->createSimple();
        $id = $oProfesorStgr->getId_item();
        $this->repository->Guardar($oProfesorStgr);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorStgrParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorStgrParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorStgr()
    {
        // Crear y guardar instancia usando factory
        $oProfesorStgr = $this->factory->createSimple();
        $id = $oProfesorStgr->getId_item();
        $this->repository->Guardar($oProfesorStgr);

        // Verificar que existe
        $oProfesorStgrExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorStgrExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorStgrExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorStgrEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorStgrEliminado);
    }

    public function test_get_profesores_stgr_sin_filtros()
    {
        $result = $this->repository->getProfesoresStgr();
        
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
