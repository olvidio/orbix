<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use Tests\myTest;
use Tests\factories\personas\PersonaExFactory;

class PgPersonaExRepositoryTest extends myTest
{
    private PersonaExRepositoryInterface $repository;
    private PersonaExFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        $this->factory = new PersonaExFactory();
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaEx = $this->factory->createSimple();
        $id = $oPersonaEx->getId_nom();
        $this->repository->Guardar($oPersonaEx);

        // Buscar por ID
        $oPersonaExEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPersonaExEncontrado);
        $this->assertInstanceOf(PersonaEx::class, $oPersonaExEncontrado);
        $this->assertEquals($id, $oPersonaExEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oPersonaExEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaEx = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaEx);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
