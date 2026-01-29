<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaSSSC;
use Tests\myTest;
use Tests\factories\personas\PersonaSSSCFactory;

class PgPersonaSSSCRepositoryTest extends myTest
{
    private PersonaSSSCRepositoryInterface $repository;
    private PersonaSSSCFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
        $this->factory = new PersonaSSSCFactory();
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaSSSC = $this->factory->createSimple();
        $id = $oPersonaSSSC->getId_nom();
        $this->repository->Guardar($oPersonaSSSC);

        // Buscar por ID
        $oPersonaSSSCEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPersonaSSSCEncontrado);
        $this->assertInstanceOf(PersonaSSSC::class, $oPersonaSSSCEncontrado);
        $this->assertEquals($id, $oPersonaSSSCEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oPersonaSSSCEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaSSSC = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaSSSC);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
