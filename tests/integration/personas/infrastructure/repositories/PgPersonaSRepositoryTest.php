<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\entity\PersonaS;
use Tests\myTest;
use Tests\factories\personas\PersonaSFactory;

class PgPersonaSRepositoryTest extends myTest
{
    private PersonaSRepositoryInterface $repository;
    private PersonaSFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        $this->factory = new PersonaSFactory();
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaS = $this->factory->createSimple();
        $id = $oPersonaS->getId_nom();
        $this->repository->Guardar($oPersonaS);

        // Buscar por ID
        $oPersonaSEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPersonaSEncontrado);
        $this->assertInstanceOf(PersonaS::class, $oPersonaSEncontrado);
        $this->assertEquals($id, $oPersonaSEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oPersonaSEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaS = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaS);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
