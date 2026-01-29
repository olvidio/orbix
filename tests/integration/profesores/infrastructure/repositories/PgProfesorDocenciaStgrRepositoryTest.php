<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use Tests\myTest;
use Tests\factories\profesores\ProfesorDocenciaStgrFactory;

class PgProfesorDocenciaStgrRepositoryTest extends myTest
{
    private ProfesorDocenciaStgrRepositoryInterface $repository;
    private ProfesorDocenciaStgrFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $this->factory = new ProfesorDocenciaStgrFactory();
    }

    public function test_guardar_nuevo_profesorDocenciaStgr()
    {
        // Crear instancia usando factory
        $oProfesorDocenciaStgr = $this->factory->createSimple();
        $id = $oProfesorDocenciaStgr->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorDocenciaStgr);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorDocenciaStgrGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDocenciaStgrGuardado);
        $this->assertEquals($id, $oProfesorDocenciaStgrGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorDocenciaStgrGuardado);
    }

    public function test_actualizar_profesorDocenciaStgr_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDocenciaStgr = $this->factory->createSimple();
        $id = $oProfesorDocenciaStgr->getId_item();
        $this->repository->Guardar($oProfesorDocenciaStgr);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorDocenciaStgrUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorDocenciaStgrUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorDocenciaStgrActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDocenciaStgrActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorDocenciaStgrActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDocenciaStgr = $this->factory->createSimple();
        $id = $oProfesorDocenciaStgr->getId_item();
        $this->repository->Guardar($oProfesorDocenciaStgr);

        // Buscar por ID
        $oProfesorDocenciaStgrEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDocenciaStgrEncontrado);
        $this->assertInstanceOf(ProfesorDocenciaStgr::class, $oProfesorDocenciaStgrEncontrado);
        $this->assertEquals($id, $oProfesorDocenciaStgrEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorDocenciaStgrEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorDocenciaStgr = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorDocenciaStgr);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDocenciaStgr = $this->factory->createSimple();
        $id = $oProfesorDocenciaStgr->getId_item();
        $this->repository->Guardar($oProfesorDocenciaStgr);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorDocenciaStgrParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorDocenciaStgrParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorDocenciaStgr()
    {
        // Crear y guardar instancia usando factory
        $oProfesorDocenciaStgr = $this->factory->createSimple();
        $id = $oProfesorDocenciaStgr->getId_item();
        $this->repository->Guardar($oProfesorDocenciaStgr);

        // Verificar que existe
        $oProfesorDocenciaStgrExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorDocenciaStgrExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorDocenciaStgrExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorDocenciaStgrEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorDocenciaStgrEliminado);
    }

    public function test_get_profesor_docencias_stgr_sin_filtros()
    {
        $result = $this->repository->getProfesorDocenciasStgr();
        
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
