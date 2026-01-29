<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use Tests\myTest;
use Tests\factories\personas\PersonaNFactory;

class PgPersonaNRepositoryTest extends myTest
{
    private PersonaNRepositoryInterface $repository;
    private PersonaNFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        $this->factory = new PersonaNFactory();
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaN = $this->factory->createSimple();
        $id = $oPersonaN->getId_nom();
        $this->repository->Guardar($oPersonaN);

        // Buscar por ID
        $oPersonaNEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPersonaNEncontrado);
        $this->assertInstanceOf(PersonaN::class, $oPersonaNEncontrado);
        $this->assertEquals($id, $oPersonaNEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oPersonaNEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaN = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaN);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
