<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaAllRepository;
use Tests\myTest;

class PgPersonaAllRepositoryTest extends myTest
{
    public function test_get_persona_by_id_nom_cuando_no_hay_coincidencias()
    {
        $repository = new PgPersonaAllRepository(
            $GLOBALS['container']->get(PersonaDlRepositoryInterface::class),
        );
        $resultado = $repository->getPersonaByIdNom(999999981);
        $this->assertFalse(
            is_object($resultado) && method_exists($resultado, 'getId_nom'),
            'No se esperaba una persona de dominio'
        );
    }
}
