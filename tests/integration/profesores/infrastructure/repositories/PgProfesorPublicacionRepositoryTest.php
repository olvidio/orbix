<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\entity\ProfesorPublicacion;
use Tests\myTest;
use Tests\factories\profesores\ProfesorPublicacionFactory;

class PgProfesorPublicacionRepositoryTest extends myTest
{
    private ProfesorPublicacionRepositoryInterface $repository;
    private ProfesorPublicacionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorPublicacionRepositoryInterface::class);
        $this->factory = new ProfesorPublicacionFactory();
    }

    public function test_guardar_nuevo_profesorPublicacion()
    {
        // Crear instancia usando factory
        $oProfesorPublicacion = $this->factory->createSimple();
        $id = $oProfesorPublicacion->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorPublicacion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorPublicacionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorPublicacionGuardado);
        $this->assertEquals($id, $oProfesorPublicacionGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorPublicacionGuardado);
    }

    public function test_actualizar_profesorPublicacion_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorPublicacion = $this->factory->createSimple();
        $id = $oProfesorPublicacion->getId_item();
        $this->repository->Guardar($oProfesorPublicacion);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorPublicacionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorPublicacionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorPublicacionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorPublicacionActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorPublicacionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorPublicacion = $this->factory->createSimple();
        $id = $oProfesorPublicacion->getId_item();
        $this->repository->Guardar($oProfesorPublicacion);

        // Buscar por ID
        $oProfesorPublicacionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorPublicacionEncontrado);
        $this->assertInstanceOf(ProfesorPublicacion::class, $oProfesorPublicacionEncontrado);
        $this->assertEquals($id, $oProfesorPublicacionEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorPublicacionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorPublicacion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorPublicacion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorPublicacion = $this->factory->createSimple();
        $id = $oProfesorPublicacion->getId_item();
        $this->repository->Guardar($oProfesorPublicacion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorPublicacionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorPublicacionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorPublicacion()
    {
        // Crear y guardar instancia usando factory
        $oProfesorPublicacion = $this->factory->createSimple();
        $id = $oProfesorPublicacion->getId_item();
        $this->repository->Guardar($oProfesorPublicacion);

        // Verificar que existe
        $oProfesorPublicacionExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorPublicacionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorPublicacionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorPublicacionEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorPublicacionEliminado);
    }

    public function test_get_profesor_publicaciones_sin_filtros()
    {
        $result = $this->repository->getProfesorPublicaciones();
        
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
