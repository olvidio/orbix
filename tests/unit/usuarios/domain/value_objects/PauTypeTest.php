<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\PauType;
use Tests\myTest;

class PauTypeTest extends myTest
{
    public function test_create_valid_pau_type_none()
    {
        $pauType = new PauType(PauType::PAU_NONE);
        $this->assertEquals(PauType::PAU_NONE, $pauType->value());
    }

    public function test_create_valid_pau_type_cdc()
    {
        $pauType = new PauType(PauType::PAU_CDC);
        $this->assertEquals(PauType::PAU_CDC, $pauType->value());
    }

    public function test_create_valid_pau_type_ctr()
    {
        $pauType = new PauType(PauType::PAU_CTR);
        $this->assertEquals(PauType::PAU_CTR, $pauType->value());
    }

    public function test_create_valid_pau_type_nom()
    {
        $pauType = new PauType(PauType::PAU_NOM);
        $this->assertEquals(PauType::PAU_NOM, $pauType->value());
    }

    public function test_create_valid_pau_type_sacd()
    {
        $pauType = new PauType(PauType::PAU_SACD);
        $this->assertEquals(PauType::PAU_SACD, $pauType->value());
    }

    public function test_empty_pau_type_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('PAU type cannot be empty');
        new PauType('');
    }

    public function test_invalid_pau_type_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PAU type');
        new PauType('invalid_type');
    }

    public function test_equals_returns_true_for_same_pau_type()
    {
        $pauType1 = new PauType(PauType::PAU_CDC);
        $pauType2 = new PauType(PauType::PAU_CDC);
        $this->assertTrue($pauType1->equals($pauType2));
    }

    public function test_equals_returns_false_for_different_pau_type()
    {
        $pauType1 = new PauType(PauType::PAU_CDC);
        $pauType2 = new PauType(PauType::PAU_CTR);
        $this->assertFalse($pauType1->equals($pauType2));
    }

    public function test_to_string_returns_pau_type_value()
    {
        $pauType = new PauType(PauType::PAU_CDC);
        $this->assertEquals(PauType::PAU_CDC, (string)$pauType);
    }
}