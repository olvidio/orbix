<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\PersonaNotaCertificadoRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use Tests\factories\notas\PersonaNotaFactory;
use Tests\myTest;

class PgPersonaNotaCertificadoRepositoryTest extends myTest
{
    private PersonaNotaCertificadoRepositoryInterface $repository;
    private PersonaNotaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaNotaCertificadoRepositoryInterface::class);
        $this->factory = new PersonaNotaFactory();
    }

    public function test_guardar_eliminar_persona_nota()
    {
        $o = $this->factory->createSimple();
        $idn = $o->getId_nom();
        $idv = $o->getId_nivel();
        $ta = $o->getTipo_acta();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($idn, $idv, $ta);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(PersonaNota::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($idn, $idv, $ta));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992, 999999993));
    }
}
