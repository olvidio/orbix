<?php

namespace Tests\integration\actividadestudios\infrastructure\repositories;

use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use Tests\myTest;
use Tests\factories\actividadestudios\MatriculaFactory;

class PgMatriculaDlRepositoryTest extends myTest
{
    private MatriculaRepositoryInterface $repository;
    private MatriculaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
        $this->factory = new MatriculaFactory();
    }

    public function test_guardar_nuevo_matricula()
    {
        // Crear instancia usando factory
        $oMatricula = $this->factory->createSimple();
        $id = $oMatricula->getActividadMatriculaPk();

        // Guardar
        $result = $this->repository->Guardar($oMatricula);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oMatriculaGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oMatriculaGuardado);
        $this->assertEquals($id, $oMatriculaGuardado->getActividadMatriculaPk());

        // Limpiar
        $this->repository->Eliminar($oMatriculaGuardado);
    }

    public function test_actualizar_matricula_existente()
    {
        // Crear y guardar instancia usando factory
        $oMatricula = $this->factory->createSimple();
        $id = $oMatricula->getActividadMatriculaPk();
        $this->repository->Guardar($oMatricula);

        // Crear otra instancia con datos diferentes para actualizar
        $oMatriculaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oMatriculaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oMatriculaActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oMatriculaActualizado);

        // Limpiar
        $this->repository->Eliminar($oMatriculaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMatricula = $this->factory->createSimple();
        $id = $oMatricula->getActividadMatriculaPk();
        $this->repository->Guardar($oMatricula);

        // Buscar por ID
        $oMatriculaEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oMatriculaEncontrado);
        $this->assertInstanceOf(Matricula::class, $oMatriculaEncontrado);
        $this->assertEquals($id, $oMatriculaEncontrado->getActividadMatriculaPk());

        // Limpiar
        $this->repository->Eliminar($oMatriculaEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMatricula = $this->factory->createSimple();
        $id = $oMatricula->getActividadMatriculaPk();
        $this->repository->Guardar($oMatricula);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id->idActiv(), $aDatos['id_activ']);

        // Limpiar
        $oMatriculaParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oMatriculaParaborrar);
    }

    public function test_eliminar_matricula()
    {
        // Crear y guardar instancia usando factory
        $oMatricula = $this->factory->createSimple();
        $id = $oMatricula->getActividadMatriculaPk();
        $this->repository->Guardar($oMatricula);

        // Verificar que existe
        $oMatriculaExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oMatriculaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oMatriculaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oMatriculaEliminado = $this->repository->findByPk($id);
        $this->assertNull($oMatriculaEliminado);
    }

    /*
    public function test_get_matriculas_pendientes_sin_filtros()
    {
        $result = $this->repository->getMatriculasPendientes();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_matriculas_sin_filtros()
    {
        $result = $this->repository->getMatriculas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

}
