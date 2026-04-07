<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaTstart;
use Tests\myTest;

class EncargoDiaTstartTest extends myTest
{
    public function test_create_valid_encargoDiaTstart()
    {
        $encargoDiaTstart = new EncargoDiaTstart('2026-07-03', '10:23');
        $this->assertEquals('2026-07-03T10:23:00+00:00', $encargoDiaTstart->format(DATE_ATOM));
    }

}
