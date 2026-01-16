<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoCtrId;
use Tests\myTest;

class EncargoCtrIdTest extends myTest
{
    public function test_create_valid_encargoCtrId()
    {
        $encargoCtrId = new EncargoCtrId('test value');
        $this->assertEquals('test value', $encargoCtrId->value());
    }

}
