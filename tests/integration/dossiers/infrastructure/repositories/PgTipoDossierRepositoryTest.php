<?php

namespace Tests\integration\dossiers\infrastructure\repositories;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use Tests\myTest;
use Tests\factories\dossiers\TipoDossierFactory;

class PgTipoDossierRepositoryTest extends myTest
{
    private TipoDossierRepositoryInterface $repository;
    private TipoDossierFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $this->factory = new TipoDossierFactory();
    }

    public function test_guardar_nuevo_tipoDossier()
    {
        // Crear instancia usando factory
        $oTipoDossier = $this->factory->createSimple();
        $id = $oTipoDossier->getId_tipo_dossier();

        // Guardar
        $result = $this->repository->Guardar($oTipoDossier);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoDossierGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDossierGuardado);
        $this->assertEquals($id, $oTipoDossierGuardado->getId_tipo_dossier());

        // Limpiar
        $this->repository->Eliminar($oTipoDossierGuardado);
    }

    public function test_actualizar_tipoDossier_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDossier = $this->factory->createSimple();
        $id = $oTipoDossier->getId_tipo_dossier();
        $this->repository->Guardar($oTipoDossier);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipoDossierUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipoDossierUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoDossierActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDossierActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoDossierActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDossier = $this->factory->createSimple();
        $id = $oTipoDossier->getId_tipo_dossier();
        $this->repository->Guardar($oTipoDossier);

        // Buscar por ID
        $oTipoDossierEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDossierEncontrado);
        $this->assertInstanceOf(TipoDossier::class, $oTipoDossierEncontrado);
        $this->assertEquals($id, $oTipoDossierEncontrado->getId_tipo_dossier());

        // Limpiar
        $this->repository->Eliminar($oTipoDossierEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoDossier = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTipoDossier);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDossier = $this->factory->createSimple();
        $id = $oTipoDossier->getId_tipo_dossier();
        $this->repository->Guardar($oTipoDossier);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tipo_dossier', $aDatos);
        $this->assertEquals($id, $aDatos['id_tipo_dossier']);

        // Limpiar
        $oTipoDossierParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoDossierParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoDossier()
    {
        // Crear y guardar instancia usando factory
        $oTipoDossier = $this->factory->createSimple();
        $id = $oTipoDossier->getId_tipo_dossier();
        $this->repository->Guardar($oTipoDossier);

        // Verificar que existe
        $oTipoDossierExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoDossierExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoDossierExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoDossierEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoDossierEliminado);
    }

    public function test_get_tipos_dossiers_sin_filtros()
    {
        $result = $this->repository->getTiposDossiers();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
