<?php

namespace Tests\unit\asistentes\domain\value_objects;

use src\asistentes\domain\value_objects\AsistenteObservEst;
use Tests\myTest;

class AsistenteObservEstTest extends myTest
{
    public function test_create_valid_asistenteObservEst()
    {
        $asistenteObservEst = new AsistenteObservEst('test value');
        $this->assertEquals('test value', $asistenteObservEst->value());
    }

    public function test_to_string_returns_asistenteObservEst_value()
    {
        $asistenteObservEst = new AsistenteObservEst('test value');
        $this->assertEquals('test value', (string)$asistenteObservEst);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asistenteObservEst = AsistenteObservEst::fromNullableString('test value');
        $this->assertInstanceOf(AsistenteObservEst::class, $asistenteObservEst);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asistenteObservEst = AsistenteObservEst::fromNullableString(null);
        $this->assertNull($asistenteObservEst);
    }

}
