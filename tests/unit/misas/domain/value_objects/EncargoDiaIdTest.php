<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaId;
use Tests\myTest;

class EncargoDiaIdTest extends myTest
{
    public function test_create_valid_encargoDiaId()
    {
        $encargoDiaId = new EncargoDiaId('test value');
        $this->assertEquals('test value', $encargoDiaId->value());
    }

}
