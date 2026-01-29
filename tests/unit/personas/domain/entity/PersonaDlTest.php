<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaDl;
use Tests\myTest;

class PersonaDlTest extends myTest
{
    private PersonaDl $PersonaDl;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaDl = new PersonaDl();
    }


}
