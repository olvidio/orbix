<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\AppsReq;
use Tests\myTest;

class AppsReqTest extends myTest
{
    public function test_create_valid_appsReq()
    {
        $appsReq = new AppsReq([7]);
        $this->assertEquals([7], $appsReq->toArray());
    }

    public function test_to_string_returns_appsReq_value()
    {
        $appsReq = new AppsReq([8, 3]);
        $this->assertEquals('8,3', (string)$appsReq);
    }

}
