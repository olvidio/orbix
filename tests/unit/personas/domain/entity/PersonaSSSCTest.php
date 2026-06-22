<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSSSC;
use Tests\myTest;

final class PersonaSSSCTest extends myTest
{
    private PersonaSSSC $persona;

    public function setUp(): void
    {
        parent::setUp();
        $this->persona = new PersonaSSSC();
    }

    public function test_extends_persona_dl(): void
    {
        $this->assertInstanceOf(PersonaDl::class, $this->persona);
    }

    public function test_get_class_name(): void
    {
        $this->assertSame('PersonaSSSC', $this->persona->getClassName());
    }

    public function test_set_and_get_id_auto(): void
    {
        $this->persona->setId_auto(123);
        $this->assertSame(123, $this->persona->getId_auto());
    }
}
