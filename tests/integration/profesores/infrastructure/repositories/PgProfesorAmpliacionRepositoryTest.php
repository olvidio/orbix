<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\entity\ProfesorAmpliacion;
use Tests\myTest;
use Tests\factories\profesores\ProfesorAmpliacionFactory;

class PgProfesorAmpliacionRepositoryTest extends myTest
{
    private ProfesorAmpliacionRepositoryInterface $repository;
    private ProfesorAmpliacionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $this->factory = new ProfesorAmpliacionFactory();
    }

    public function test_guardar_nuevo_profesorAmpliacion()
    {
        // Crear instancia usando factory
        $oProfesorAmpliacion = $this->factory->createSimple();
        $id = $oProfesorAmpliacion->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oProfesorAmpliacion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorAmpliacionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorAmpliacionGuardado);
        $this->assertEquals($id, $oProfesorAmpliacionGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorAmpliacionGuardado);
    }

    public function test_actualizar_profesorAmpliacion_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorAmpliacion = $this->factory->createSimple();
        $id = $oProfesorAmpliacion->getId_item();
        $this->repository->Guardar($oProfesorAmpliacion);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorAmpliacionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorAmpliacionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorAmpliacionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorAmpliacionActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorAmpliacionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorAmpliacion = $this->factory->createSimple();
        $id = $oProfesorAmpliacion->getId_item();
        $this->repository->Guardar($oProfesorAmpliacion);

        // Buscar por ID
        $oProfesorAmpliacionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorAmpliacionEncontrado);
        $this->assertInstanceOf(ProfesorAmpliacion::class, $oProfesorAmpliacionEncontrado);
        $this->assertEquals($id, $oProfesorAmpliacionEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oProfesorAmpliacionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorAmpliacion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorAmpliacion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorAmpliacion = $this->factory->createSimple();
        $id = $oProfesorAmpliacion->getId_item();
        $this->repository->Guardar($oProfesorAmpliacion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oProfesorAmpliacionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorAmpliacionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorAmpliacion()
    {
        // Crear y guardar instancia usando factory
        $oProfesorAmpliacion = $this->factory->createSimple();
        $id = $oProfesorAmpliacion->getId_item();
        $this->repository->Guardar($oProfesorAmpliacion);

        // Verificar que existe
        $oProfesorAmpliacionExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorAmpliacionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorAmpliacionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorAmpliacionEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorAmpliacionEliminado);
    }

    public function test_get_array_profesores_asignatura_sin_filtros()
    {
        $result = $this->repository->getArrayProfesoresAsignatura(1001);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_profesores_asignatura_vo_sin_filtros()
    {
        $result = $this->repository->getArrayProfesoresAsignaturaVo(AsignaturaId::fromNullableInt(1001));
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_profesor_ampliaciones_sin_filtros()
    {
        $result = $this->repository->getProfesorAmpliaciones();
        
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
