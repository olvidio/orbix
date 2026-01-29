<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\Persona;
use Tests\myTest;

class PersonaTest extends myTest
{
    private Persona $Persona;

    public function setUp(): void
    {
        parent::setUp();
        $this->Persona = new Persona();
    }

}
