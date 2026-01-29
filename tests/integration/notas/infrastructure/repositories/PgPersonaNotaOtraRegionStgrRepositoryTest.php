<?php

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use Tests\factories\notas\PersonaNotaOtraRegionStgrFactory;
use Tests\myTest;

class PgPersonaNotaOtraRegionStgrRepositoryTest extends myTest
{
    private PersonaNotaOtraRegionStgrRepositoryInterface $repository;
    private PersonaNotaOtraRegionStgrFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $esquema_region_stgr = 'H-Hv';
        $this->repository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $this->factory = new PersonaNotaOtraRegionStgrFactory();
    }

    public function test_guardar_nuevo_personaNotaOtraRegionStgr()
    {
        // Crear instancia usando factory
        $oPersonaNotaOtraRegionStgr = $this->factory->createSimple();
        $id = $oPersonaNotaOtraRegionStgr->getPersonaNotaPk();

        // Guardar
        $result = $this->repository->Guardar($oPersonaNotaOtraRegionStgr);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oPersonaNotaOtraRegionStgrGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaOtraRegionStgrGuardado);
        $this->assertEquals($id, $oPersonaNotaOtraRegionStgrGuardado->getPersonaNotaPk());

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaOtraRegionStgrGuardado);
    }

    public function test_actualizar_personaNotaOtraRegionStgr_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNotaOtraRegionStgr = $this->factory->createSimple();
        $id = $oPersonaNotaOtraRegionStgr->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNotaOtraRegionStgr);

        // Crear otra instancia con datos diferentes para actualizar
        $oPersonaNotaOtraRegionStgrUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oPersonaNotaOtraRegionStgrUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oPersonaNotaOtraRegionStgrActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaOtraRegionStgrActualizado);

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaOtraRegionStgrActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNotaOtraRegionStgr = $this->factory->createSimple();
        $id = $oPersonaNotaOtraRegionStgr->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNotaOtraRegionStgr);

        // Buscar por ID
        $oPersonaNotaOtraRegionStgrEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaOtraRegionStgrEncontrado);
        $this->assertInstanceOf(PersonaNotaOtraRegionStgr::class, $oPersonaNotaOtraRegionStgrEncontrado);
        $this->assertEquals($id, $oPersonaNotaOtraRegionStgrEncontrado->getPersonaNotaPk());

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaOtraRegionStgrEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNotaOtraRegionStgr = $this->factory->createSimple();
        $id = $oPersonaNotaOtraRegionStgr->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNotaOtraRegionStgr);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_nivel', $aDatos);
        $this->assertEquals($id->idNivel(), $aDatos['id_nivel']);

        // Limpiar
        $oPersonaNotaOtraRegionStgrParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oPersonaNotaOtraRegionStgrParaborrar);
    }

    public function test_eliminar_personaNotaOtraRegionStgr()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNotaOtraRegionStgr = $this->factory->createSimple();
        $id = $oPersonaNotaOtraRegionStgr->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNotaOtraRegionStgr);

        // Verificar que existe
        $oPersonaNotaOtraRegionStgrExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaOtraRegionStgrExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPersonaNotaOtraRegionStgrExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPersonaNotaOtraRegionStgrEliminado = $this->repository->findByPk($id);
        $this->assertNull($oPersonaNotaOtraRegionStgrEliminado);
    }


}
