<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaTstart;
use Tests\myTest;

class EncargoDiaTstartTest extends myTest
{
    public function test_create_valid_encargoDiaTstart()
    {
        $encargoDiaTstart = new EncargoDiaTstart('test value');
        $this->assertEquals('test value', $encargoDiaTstart->value());
    }

}
