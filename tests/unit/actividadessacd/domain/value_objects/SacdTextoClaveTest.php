<?php

namespace Tests\unit\actividadessacd\domain\value_objects;

use src\actividadessacd\domain\value_objects\SacdTextoClave;
use Tests\myTest;

class SacdTextoClaveTest extends myTest
{
    public function test_create_valid_sacdTextoClave()
    {
        $sacdTextoClave = new SacdTextoClave('test value');
        $this->assertEquals('test value', $sacdTextoClave->value());
    }

    public function test_equals_returns_true_for_same_sacdTextoClave()
    {
        $sacdTextoClave1 = new SacdTextoClave('test value');
        $sacdTextoClave2 = new SacdTextoClave('test value');
        $this->assertTrue($sacdTextoClave1->equals($sacdTextoClave2));
    }

    public function test_equals_returns_false_for_different_sacdTextoClave()
    {
        $sacdTextoClave1 = new SacdTextoClave('test value');
        $sacdTextoClave2 = new SacdTextoClave('alternative value');
        $this->assertFalse($sacdTextoClave1->equals($sacdTextoClave2));
    }

    public function test_to_string_returns_sacdTextoClave_value()
    {
        $sacdTextoClave = new SacdTextoClave('test value');
        $this->assertEquals('test value', (string)$sacdTextoClave);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $sacdTextoClave = SacdTextoClave::fromNullableString('test value');
        $this->assertInstanceOf(SacdTextoClave::class, $sacdTextoClave);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $sacdTextoClave = SacdTextoClave::fromNullableString(null);
        $this->assertNull($sacdTextoClave);
    }

}
