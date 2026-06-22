<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaEx;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\SituacionCode;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

final class PersonaExTest extends myTest
{
    private PersonaEx $persona;

    public function setUp(): void
    {
        parent::setUp();
        $this->persona = new PersonaEx();
        $this->persona->setId_schema(1);
        $this->persona->setId_nom(2);
        $this->persona->setIdTablaVo(new PersonaTablaCode('dl'));
        $this->persona->setApellido1Vo(new PersonaApellido1Text('Apellido'));
        $this->persona->setSituacionVo(SituacionCode::fromNullableString('A'));
    }

    public function test_get_class_name(): void
    {
        $this->assertSame('PersonaEx', $this->persona->getClassName());
    }

    public function test_set_and_get_id_schema(): void
    {
        $this->persona->setId_schema(10);
        $this->assertSame(10, $this->persona->getId_schema());
    }

    public function test_set_and_get_id_nom(): void
    {
        $this->persona->setId_nom(20);
        $this->assertSame(20, $this->persona->getId_nom());
    }

    public function test_set_and_get_id_auto(): void
    {
        $this->persona->setId_auto(99);
        $this->assertSame(99, $this->persona->getId_auto());
    }

    public function test_set_and_get_dl_vo(): void
    {
        $this->persona->setDlVo(new DelegacionCode('dlb'));
        $this->assertSame('dlb', $this->persona->getDlVo()?->value());
    }

    public function test_get_apellidos_basico(): void
    {
        $this->assertStringContainsString('Apellido', $this->persona->getApellidos());
    }
}
