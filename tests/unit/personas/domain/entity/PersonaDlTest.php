<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\value_objects\PersonaTablaCode;
use Tests\myTest;

final class PersonaDlTest extends myTest
{
    private PersonaDl $persona;

    public function setUp(): void
    {
        parent::setUp();
        $this->persona = new PersonaDl();
    }

    public function test_extends_persona_global(): void
    {
        $this->assertInstanceOf(PersonaGlobal::class, $this->persona);
    }

    public function test_get_class_name(): void
    {
        $this->assertSame('PersonaDl', $this->persona->getClassName());
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

    public function test_set_and_get_id_tabla_vo(): void
    {
        $this->persona->setIdTablaVo(new PersonaTablaCode('dl'));
        $this->assertSame('dl', $this->persona->getIdTablaVo()->value());
    }
}
