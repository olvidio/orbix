<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaSSSC;
use Tests\myTest;

class PersonaSSSCTest extends myTest
{
    private PersonaSSSC $PersonaSSSC;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaSSSC = new PersonaSSSC();
    }

}
