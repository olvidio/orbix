<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ModsReq;
use Tests\myTest;

class ModsReqTest extends myTest
{
    public function test_create_valid_modsReq()
    {
        $modsReq = new ModsReq([3]);
        $this->assertEquals([3], $modsReq->toArray());
    }

    public function test_to_string_returns_modsReq_value()
    {
        $modsReq = new ModsReq([4,7]);
        $this->assertEquals('{4,7}', (string)$modsReq);
    }

}
