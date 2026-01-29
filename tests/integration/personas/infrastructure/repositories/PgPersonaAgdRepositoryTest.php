<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use Tests\myTest;
use Tests\factories\personas\PersonaAgdFactory;

class PgPersonaAgdRepositoryTest extends myTest
{
    private PersonaAgdRepositoryInterface $repository;
    private PersonaAgdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
        $this->factory = new PersonaAgdFactory();
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaAgd = $this->factory->create();
        $id = $oPersonaAgd->getId_nom();
        $this->repository->Guardar($oPersonaAgd);

        // Buscar por ID
        $oPersonaAgdEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPersonaAgdEncontrado);
        $this->assertInstanceOf(PersonaAgd::class, $oPersonaAgdEncontrado);
        $this->assertEquals($id, $oPersonaAgdEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oPersonaAgdEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaAgd = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaAgd);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
