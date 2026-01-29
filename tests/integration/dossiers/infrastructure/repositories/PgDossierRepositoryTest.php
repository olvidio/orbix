<?php

namespace Tests\integration\dossiers\infrastructure\repositories;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use Tests\myTest;
use Tests\factories\dossiers\DossierFactory;

class PgDossierRepositoryTest extends myTest
{
    private DossierRepositoryInterface $repository;
    private DossierFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $this->factory = new DossierFactory();
    }

    public function test_guardar_nuevo_dossier()
    {
        // Crear instancia usando factory
        $oDossier = $this->factory->createSimple();
        $id = $oDossier->getDossierPk();

        // Guardar
        $result = $this->repository->Guardar($oDossier);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oDossierGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oDossierGuardado);
        $this->assertEquals($id, $oDossierGuardado->getDossierPk());

        // Limpiar
        $this->repository->Eliminar($oDossierGuardado);
    }

    public function test_actualizar_dossier_existente()
    {
        // Crear y guardar instancia usando factory
        $oDossier = $this->factory->createSimple();
        $id = $oDossier->getDossierPk();
        $this->repository->Guardar($oDossier);

        // Crear otra instancia con datos diferentes para actualizar
        $oDossierUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oDossierUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oDossierActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oDossierActualizado);

        // Limpiar
        $this->repository->Eliminar($oDossierActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDossier = $this->factory->createSimple();
        $id = $oDossier->getDossierPk();
        $this->repository->Guardar($oDossier);

        // Buscar por ID
        $oDossierEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oDossierEncontrado);
        $this->assertInstanceOf(Dossier::class, $oDossierEncontrado);
        $this->assertEquals($id, $oDossierEncontrado->getDossierPk());

        // Limpiar
        $this->repository->Eliminar($oDossierEncontrado);
    }

    public function test_eliminar_dossier()
    {
        // Crear y guardar instancia usando factory
        $oDossier = $this->factory->createSimple();
        $id = $oDossier->getDossierPk();
        $this->repository->Guardar($oDossier);

        // Verificar que existe
        $oDossierExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oDossierExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oDossierExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oDossierEliminado = $this->repository->findByPk($id);
        $this->assertNull($oDossierEliminado);
    }

}
