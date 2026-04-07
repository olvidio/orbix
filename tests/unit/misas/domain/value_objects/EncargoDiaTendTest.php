<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaTend;
use Tests\myTest;

class EncargoDiaTendTest extends myTest
{
    public function test_create_valid_encargoDiaTend()
    {
        $encargoDiaTend = new EncargoDiaTend('2026-07-03', '10:23');
        $this->assertEquals('2026-07-03T10:23:00+00:00', $encargoDiaTend->format(DATE_ATOM));
    }

}
