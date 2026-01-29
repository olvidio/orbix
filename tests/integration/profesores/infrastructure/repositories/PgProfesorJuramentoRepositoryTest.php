<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\entity\ProfesorJuramento;
use Tests\myTest;
use Tests\factories\profesores\ProfesorJuramentoFactory;

class PgProfesorJuramentoRepositoryTest extends myTest
{
    private ProfesorJuramentoRepositoryInterface $repository;
    private ProfesorJuramentoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorJuramentoRepositoryInterface::class);
        $this->factory = new ProfesorJuramentoFactory();
    }

    public function test_guardar_nuevo_profesorJuramento()
    {
        // Crear instancia usando factory
        $oProfesorJuramento = $this->factory->createSimple();
        $id = $oProfesorJuramento->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorJuramento);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorJuramentoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorJuramentoGuardado);
        $this->assertEquals($id, $oProfesorJuramentoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorJuramentoGuardado);
    }

    public function test_actualizar_profesorJuramento_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorJuramento = $this->factory->createSimple();
        $id = $oProfesorJuramento->getId_item();
        $this->repository->Guardar($oProfesorJuramento);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorJuramentoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorJuramentoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorJuramentoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorJuramentoActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorJuramentoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorJuramento = $this->factory->createSimple();
        $id = $oProfesorJuramento->getId_item();
        $this->repository->Guardar($oProfesorJuramento);

        // Buscar por ID
        $oProfesorJuramentoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorJuramentoEncontrado);
        $this->assertInstanceOf(ProfesorJuramento::class, $oProfesorJuramentoEncontrado);
        $this->assertEquals($id, $oProfesorJuramentoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorJuramentoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorJuramento = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorJuramento);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorJuramento = $this->factory->createSimple();
        $id = $oProfesorJuramento->getId_item();
        $this->repository->Guardar($oProfesorJuramento);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorJuramentoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorJuramentoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorJuramento()
    {
        // Crear y guardar instancia usando factory
        $oProfesorJuramento = $this->factory->createSimple();
        $id = $oProfesorJuramento->getId_item();
        $this->repository->Guardar($oProfesorJuramento);

        // Verificar que existe
        $oProfesorJuramentoExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorJuramentoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorJuramentoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorJuramentoEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorJuramentoEliminado);
    }

    public function test_get_profesor_juramentos_sin_filtros()
    {
        $result = $this->repository->getProfesorJuramentos();
        
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
