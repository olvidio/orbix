<?php

namespace Tests\integration\dossiers\infrastructure\persistence\postgresql;

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

    /**
     * Regresión: el UPDATE de `Guardar` filtraba solo por `tabla`, así que abrir o cerrar un dossier
     * cambiaba el estado y las fechas de **todas** las marcas de esa tabla (todas las personas de la dl).
     */
    public function test_actualizar_un_dossier_no_cambia_los_demas(): void
    {
        $uno = new Dossier();
        $uno->setTabla('p');
        $uno->setId_pau(999999001);
        $uno->setId_tipo_dossier(3005);
        $otro = new Dossier();
        $otro->setTabla('p');
        $otro->setId_pau(999999002);
        $otro->setId_tipo_dossier(3005);
        $this->assertNull($this->repository->findByPk($uno->getDossierPk()), 'el id de prueba no existe en la BD');
        $this->assertNull($this->repository->findByPk($otro->getDossierPk()), 'el id de prueba no existe en la BD');

        try {
            $uno->abrir();
            $otro->abrir();
            $this->assertTrue($this->repository->Guardar($uno));
            $this->assertTrue($this->repository->Guardar($otro));

            $uno->setActive(false);
            $uno->setF_active(null);
            $this->assertTrue($this->repository->Guardar($uno));

            $unoGuardado = $this->repository->findByPk($uno->getDossierPk());
            $otroGuardado = $this->repository->findByPk($otro->getDossierPk());
            $this->assertNotNull($unoGuardado);
            $this->assertNotNull($otroGuardado);
            $this->assertFalse($unoGuardado->isActive(), 'el dossier modificado queda cerrado');
            $this->assertTrue($otroGuardado->isActive(), 'el otro dossier de la misma tabla sigue abierto');
            $this->assertNotNull($otroGuardado->getF_active());
        } finally {
            $this->repository->Eliminar($uno);
            $this->repository->Eliminar($otro);
        }
    }
}
