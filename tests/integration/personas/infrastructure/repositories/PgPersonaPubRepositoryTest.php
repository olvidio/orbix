<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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

    public function test_personas_para_listado_sigue_el_criterio()
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(PersonaField::IdNom, 99999999));
        $problemas = [];
        $sinRegion = [];

        $this->assertSame([], $this->repository->getPersonasParaListado($criteria, $problemas, $sinRegion));

        $criteria = Criteria::create()
            ->where(Filter::eq(PersonaField::Situacion, 'A'))
            ->orderBy(PersonaField::Dl)
            ->addOrderBy(PersonaField::Apellido1)
            ->limit(3);
        $lista = $this->repository->getPersonasParaListado($criteria, $problemas, $sinRegion);

        $this->assertLessThanOrEqual(3, count($lista));
        foreach ($lista as $persona) {
            $this->assertInstanceOf(PersonaPub::class, $persona);
            $this->assertSame('A', $persona->getSituacion());
        }
    }


}
