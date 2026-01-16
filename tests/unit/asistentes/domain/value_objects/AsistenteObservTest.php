<?php

namespace Tests\unit\asistentes\domain\value_objects;

use src\asistentes\domain\value_objects\AsistenteObserv;
use Tests\myTest;

class AsistenteObservTest extends myTest
{
    public function test_create_valid_asistenteObserv()
    {
        $asistenteObserv = new AsistenteObserv('test value');
        $this->assertEquals('test value', $asistenteObserv->value());
    }

    public function test_to_string_returns_asistenteObserv_value()
    {
        $asistenteObserv = new AsistenteObserv('test value');
        $this->assertEquals('test value', (string)$asistenteObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asistenteObserv = AsistenteObserv::fromNullableString('test value');
        $this->assertInstanceOf(AsistenteObserv::class, $asistenteObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asistenteObserv = AsistenteObserv::fromNullableString(null);
        $this->assertNull($asistenteObserv);
    }

}
