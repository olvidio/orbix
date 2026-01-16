<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\MetaMenuParametros;
use Tests\myTest;

class MetaMenuParametrosTest extends myTest
{
    public function test_create_valid_metaMenuParametros()
    {
        $metaMenuParametros = new MetaMenuParametros('test value');
        $this->assertEquals('test value', $metaMenuParametros->value());
    }

    public function test_equals_returns_true_for_same_metaMenuParametros()
    {
        $metaMenuParametros1 = new MetaMenuParametros('test value');
        $metaMenuParametros2 = new MetaMenuParametros('test value');
        $this->assertTrue($metaMenuParametros1->equals($metaMenuParametros2));
    }

    public function test_equals_returns_false_for_different_metaMenuParametros()
    {
        $metaMenuParametros1 = new MetaMenuParametros('test value');
        $metaMenuParametros2 = new MetaMenuParametros('alternative value');
        $this->assertFalse($metaMenuParametros1->equals($metaMenuParametros2));
    }

    public function test_to_string_returns_metaMenuParametros_value()
    {
        $metaMenuParametros = new MetaMenuParametros('test value');
        $this->assertEquals('test value', (string)$metaMenuParametros);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $metaMenuParametros = MetaMenuParametros::fromNullableString('test value');
        $this->assertInstanceOf(MetaMenuParametros::class, $metaMenuParametros);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $metaMenuParametros = MetaMenuParametros::fromNullableString(null);
        $this->assertNull($metaMenuParametros);
    }

}
