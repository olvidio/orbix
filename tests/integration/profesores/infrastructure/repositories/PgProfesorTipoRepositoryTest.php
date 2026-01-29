<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\entity\ProfesorTipo;
use Tests\myTest;
use Tests\factories\profesores\ProfesorTipoFactory;

class PgProfesorTipoRepositoryTest extends myTest
{
    private ProfesorTipoRepositoryInterface $repository;
    private ProfesorTipoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
        $this->factory = new ProfesorTipoFactory();
    }

    public function test_guardar_nuevo_profesorTipo()
    {
        // Crear instancia usando factory
        $oProfesorTipo = $this->factory->createSimple();
        $id = $oProfesorTipo->getId_tipo_profesor();

        // Guardar
        $result = $this->repository->Guardar($oProfesorTipo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorTipoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTipoGuardado);
        $this->assertEquals($id, $oProfesorTipoGuardado->getId_tipo_profesor());

        // Limpiar
        $this->repository->Eliminar($oProfesorTipoGuardado);
    }

    public function test_actualizar_profesorTipo_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTipo = $this->factory->createSimple();
        $id = $oProfesorTipo->getId_tipo_profesor();
        $this->repository->Guardar($oProfesorTipo);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorTipoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorTipoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorTipoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTipoActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorTipoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTipo = $this->factory->createSimple();
        $id = $oProfesorTipo->getId_tipo_profesor();
        $this->repository->Guardar($oProfesorTipo);

        // Buscar por ID
        $oProfesorTipoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTipoEncontrado);
        $this->assertInstanceOf(ProfesorTipo::class, $oProfesorTipoEncontrado);
        $this->assertEquals($id, $oProfesorTipoEncontrado->getId_tipo_profesor());

        // Limpiar
        $this->repository->Eliminar($oProfesorTipoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorTipo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorTipo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTipo = $this->factory->createSimple();
        $id = $oProfesorTipo->getId_tipo_profesor();
        $this->repository->Guardar($oProfesorTipo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tipo_profesor', $aDatos);
        $this->assertEquals($id, $aDatos['id_tipo_profesor']);

        // Limpiar
        $oProfesorTipoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorTipoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorTipo()
    {
        // Crear y guardar instancia usando factory
        $oProfesorTipo = $this->factory->createSimple();
        $id = $oProfesorTipo->getId_tipo_profesor();
        $this->repository->Guardar($oProfesorTipo);

        // Verificar que existe
        $oProfesorTipoExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorTipoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorTipoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorTipoEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorTipoEliminado);
    }

    public function test_get_array_profesor_tipos_sin_filtros()
    {
        $result = $this->repository->getArrayProfesorTipos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_profesor_tipos_sin_filtros()
    {
        $result = $this->repository->getProfesorTipos();
        
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
