<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use Tests\factories\personas\PersonaSacdFactory;
use Tests\myTest;

class PgPersonaSacdRepositoryTest extends myTest
{
    private PersonaSacdRepositoryInterface $repository;
    private PersonaSacdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $this->factory = new PersonaSacdFactory();
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
        $this->assertInstanceOf(PersonaSacd::class, $oPersonaExEncontrado);
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

    public function test_get_sacds_by_select_sin_filtros()
    {
        $result = $this->repository->getSacdsBySelect(2);

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_sacdy_check_box_sin_filtros()
    {
        $result = $this->repository->getArraySacdyCheckBox(4);

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
