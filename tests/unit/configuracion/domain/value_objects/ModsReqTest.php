<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ModsReq;
use Tests\myTest;

class ModsReqTest extends myTest
{
    public function test_create_valid_modsReq()
    {
        $modsReq = new ModsReq('test value');
        $this->assertEquals('test value', $modsReq->value());
    }

    public function test_to_string_returns_modsReq_value()
    {
        $modsReq = new ModsReq('test value');
        $this->assertEquals('test value', (string)$modsReq);
    }

}
