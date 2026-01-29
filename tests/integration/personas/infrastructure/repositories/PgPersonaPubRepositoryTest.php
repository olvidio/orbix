<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use Tests\myTest;
use Tests\factories\personas\PersonaPubFactory;

class PgPersonaPubRepositoryTest extends myTest
{
    private PersonaPubRepositoryInterface $repository;
    private PersonaPubFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
        $this->factory = new PersonaPubFactory();
    }

    /*
     *********** Solamente deben ser test de lectura ************
     */

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaPub = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaPub);
    }


    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }


}
