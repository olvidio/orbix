<?php

namespace Tests\integration\actividadestudios\infrastructure\repositories;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use Tests\myTest;
use Tests\factories\actividadestudios\ActividadAsignaturaFactory;

class PgActividadAsignaturaDlRepositoryTest extends myTest
{
    private ActividadAsignaturaRepositoryInterface $repository;
    private ActividadAsignaturaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $this->factory = new ActividadAsignaturaFactory();
    }

    public function test_guardar_nuevo_actividadAsignatura()
    {
        // Crear instancia usando factory
        $oActividadAsignatura = $this->factory->createSimple();
        $id = $oActividadAsignatura->getActividadAsignaturaPk();

        // Guardar
        $result = $this->repository->Guardar($oActividadAsignatura);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadAsignaturaGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oActividadAsignaturaGuardado);
        $this->assertEquals($id, $oActividadAsignaturaGuardado->getActividadAsignaturaPk());

        // Limpiar
        $this->repository->Eliminar($oActividadAsignaturaGuardado);
    }

    public function test_actualizar_actividadAsignatura_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAsignatura = $this->factory->createSimple();
        $id = $oActividadAsignatura->getActividadAsignaturaPk();
        $this->repository->Guardar($oActividadAsignatura);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadAsignaturaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadAsignaturaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadAsignaturaActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oActividadAsignaturaActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadAsignaturaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAsignatura = $this->factory->createSimple();
        $id = $oActividadAsignatura->getActividadAsignaturaPk();
        $this->repository->Guardar($oActividadAsignatura);

        // Buscar por ID
        $oActividadAsignaturaEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oActividadAsignaturaEncontrado);
        $this->assertInstanceOf(ActividadAsignatura::class, $oActividadAsignaturaEncontrado);
        $this->assertEquals($id, $oActividadAsignaturaEncontrado->getActividadAsignaturaPk());

        // Limpiar
        $this->repository->Eliminar($oActividadAsignaturaEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAsignatura = $this->factory->createSimple();
        $id = $oActividadAsignatura->getActividadAsignaturaPk();
        $this->repository->Guardar($oActividadAsignatura);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id->IdActiv(), $aDatos['id_activ']);

        // Limpiar
        $oActividadAsignaturaParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oActividadAsignaturaParaborrar);
    }


    public function test_eliminar_actividadAsignatura()
    {
        // Crear y guardar instancia usando factory
        $oActividadAsignatura = $this->factory->createSimple();
        $id = $oActividadAsignatura->getActividadAsignaturaPk();
        $this->repository->Guardar($oActividadAsignatura);

        // Verificar que existe
        $oActividadAsignaturaExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oActividadAsignaturaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadAsignaturaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadAsignaturaEliminado = $this->repository->findByPk($id);
        $this->assertNull($oActividadAsignaturaEliminado);
    }

    public function test_get_asignaturas_ca_sin_filtros()
    {
        $result = $this->repository->getAsignaturasCa(3001145);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividad_asignaturas_sin_filtros()
    {
        $result = $this->repository->getActividadAsignaturas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
