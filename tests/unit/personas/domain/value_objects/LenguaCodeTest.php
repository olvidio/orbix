<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\LenguaCode;
use Tests\myTest;

class LenguaCodeTest extends myTest
{
    // LenguaCode must follow the format: xx_XX.ENCODING (e.g., es_ES.UTF-8)
    public function test_create_valid_lenguaCode()
    {
        $lenguaCode = new LenguaCode('es_ES.UTF-8');
        $this->assertEquals('es_ES.UTF-8', $lenguaCode->value());
    }

    public function test_to_string_returns_lenguaCode_value()
    {
        $lenguaCode = new LenguaCode('es_ES.UTF-8');
        $this->assertEquals('es_ES.UTF-8', (string)$lenguaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lenguaCode = LenguaCode::fromNullableString('es_ES.UTF-8');
        $this->assertInstanceOf(LenguaCode::class, $lenguaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lenguaCode = LenguaCode::fromNullableString(null);
        $this->assertNull($lenguaCode);
    }

}
