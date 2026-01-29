<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\entity\ProfesorTituloEst;
use Tests\myTest;
use Tests\factories\profesores\ProfesorTituloEstFactory;

class PgProfesorTituloEstRepositoryTest extends myTest
{
    private ProfesorTituloEstRepositoryInterface $repository;
    private ProfesorTituloEstFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorTituloEstRepositoryInterface::class);
        $this->factory = new ProfesorTituloEstFactory();
    }

    public function test_guardar_nuevo_profesorTituloEst()
    {
        // Crear instancia usando factory
        $oProfesorTituloEst = $this->factory->createSimple();
        $id = $oProfesorTituloEst->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorTituloEst);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorTituloEstGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTituloEstGuardado);
        $this->assertEquals($id, $oProfesorTituloEstGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorTituloEstGuardado);
    }

    public function test_actualizar_profesorTituloEst_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTituloEst = $this->factory->createSimple();
        $id = $oProfesorTituloEst->getId_item();
        $this->repository->Guardar($oProfesorTituloEst);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorTituloEstUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorTituloEstUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorTituloEstActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTituloEstActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorTituloEstActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTituloEst = $this->factory->createSimple();
        $id = $oProfesorTituloEst->getId_item();
        $this->repository->Guardar($oProfesorTituloEst);

        // Buscar por ID
        $oProfesorTituloEstEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTituloEstEncontrado);
        $this->assertInstanceOf(ProfesorTituloEst::class, $oProfesorTituloEstEncontrado);
        $this->assertEquals($id, $oProfesorTituloEstEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorTituloEstEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorTituloEst = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorTituloEst);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTituloEst = $this->factory->createSimple();
        $id = $oProfesorTituloEst->getId_item();
        $this->repository->Guardar($oProfesorTituloEst);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorTituloEstParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorTituloEstParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorTituloEst()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTituloEst = $this->factory->createSimple();
        $id = $oProfesorTituloEst->getId_item();
        $this->repository->Guardar($oProfesorTituloEst);

        // Verificar que existe
        $oProfesorTituloEstExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTituloEstExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorTituloEstExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorTituloEstEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorTituloEstEliminado);
    }

    public function test_get_profesor_titulos_est_sin_filtros()
    {
        $result = $this->repository->getProfesorTitulosEst();
        
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
