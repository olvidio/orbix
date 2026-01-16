<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\AppsReq;
use Tests\myTest;

class AppsReqTest extends myTest
{
    public function test_create_valid_appsReq()
    {
        $appsReq = new AppsReq('test value');
        $this->assertEquals('test value', $appsReq->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AppsReq(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_appsReq_value()
    {
        $appsReq = new AppsReq('test value');
        $this->assertEquals('test value', (string)$appsReq);
    }

}
