<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaEx;
use Tests\myTest;

class PersonaExTest extends myTest
{
    private PersonaEx $PersonaEx;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaEx = new PersonaEx();
    }


}
