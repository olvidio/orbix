<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Observ;
use Tests\myTest;

class ObservTest extends myTest
{
    public function test_create_valid_observ()
    {
        $observ = new Observ('test value');
        $this->assertEquals('test value', $observ->value());
    }

    public function test_to_string_returns_observ_value()
    {
        $observ = new Observ('test value');
        $this->assertEquals('test value', (string)$observ);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observ = Observ::fromNullableString('test value');
        $this->assertInstanceOf(Observ::class, $observ);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observ = Observ::fromNullableString(null);
        $this->assertNull($observ);
    }

}
