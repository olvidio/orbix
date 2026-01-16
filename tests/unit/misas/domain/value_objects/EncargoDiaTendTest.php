<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaTend;
use Tests\myTest;

class EncargoDiaTendTest extends myTest
{
    public function test_create_valid_encargoDiaTend()
    {
        $encargoDiaTend = new EncargoDiaTend('test value');
        $this->assertEquals('test value', $encargoDiaTend->value());
    }

}
