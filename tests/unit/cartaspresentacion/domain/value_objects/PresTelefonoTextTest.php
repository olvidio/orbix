<?php

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresTelefonoText;
use Tests\myTest;

class PresTelefonoTextTest extends myTest
{
    public function test_create_valid_presTelefonoText()
    {
        $presTelefonoText = new PresTelefonoText('test value');
        $this->assertEquals('test value', $presTelefonoText->value());
    }

    public function test_to_string_returns_presTelefonoText_value()
    {
        $presTelefonoText = new PresTelefonoText('test value');
        $this->assertEquals('test value', (string)$presTelefonoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $presTelefonoText = PresTelefonoText::fromNullableString('test value');
        $this->assertInstanceOf(PresTelefonoText::class, $presTelefonoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $presTelefonoText = PresTelefonoText::fromNullableString(null);
        $this->assertNull($presTelefonoText);
    }

}
