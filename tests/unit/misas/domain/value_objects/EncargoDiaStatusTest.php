<?php

namespace Tests\unit\misas\domain\value_objects;

use src\misas\domain\value_objects\EncargoDiaStatus;
use Tests\myTest;

class EncargoDiaStatusTest extends myTest
{
    public function test_create_valid_status_propuesta()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertEquals(1, $status->value());
    }

    public function test_create_valid_status_comunicado_sacd()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_COMUNICADO_SACD);
        $this->assertEquals(2, $status->value());
    }

    public function test_create_valid_status_comunicado_ctr()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_COMUNICADO_CTR);
        $this->assertEquals(3, $status->value());
    }

    public function test_invalid_status_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status value: 999');
        new EncargoDiaStatus(999);
    }

    public function test_equals_returns_true_for_same_status()
    {
        $status1 = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $status2 = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertTrue($status1->equals($status2));
    }

    public function test_equals_returns_false_for_different_status()
    {
        $status1 = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $status2 = new EncargoDiaStatus(EncargoDiaStatus::STATUS_COMUNICADO_SACD);
        $this->assertFalse($status1->equals($status2));
    }

    public function test_fromInt_returns_instance_for_valid_value()
    {
        $status = EncargoDiaStatus::fromInt(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertInstanceOf(EncargoDiaStatus::class, $status);
        $this->assertEquals(1, $status->value());
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $status = EncargoDiaStatus::fromNullableInt(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertInstanceOf(EncargoDiaStatus::class, $status);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $status = EncargoDiaStatus::fromNullableInt(null);
        $this->assertNull($status);
    }

    public function test_propuesta_static_method()
    {
        $status = EncargoDiaStatus::propuesta();
        $this->assertEquals(EncargoDiaStatus::STATUS_PROPUESTA, $status->value());
        $this->assertTrue($status->isPropuesta());
    }

    public function test_comunicadoSacd_static_method()
    {
        $status = EncargoDiaStatus::comunicadoSacd();
        $this->assertEquals(EncargoDiaStatus::STATUS_COMUNICADO_SACD, $status->value());
        $this->assertTrue($status->isComunicadoSacd());
    }

    public function test_comunicadoCtr_static_method()
    {
        $status = EncargoDiaStatus::comunicadoCtr();
        $this->assertEquals(EncargoDiaStatus::STATUS_COMUNICADO_CTR, $status->value());
        $this->assertTrue($status->isComunicadoCtr());
    }

    public function test_isPropuesta_returns_true_for_propuesta_status()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertTrue($status->isPropuesta());
        $this->assertFalse($status->isComunicadoSacd());
        $this->assertFalse($status->isComunicadoCtr());
    }

    public function test_isComunicadoSacd_returns_true_for_comunicado_sacd_status()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_COMUNICADO_SACD);
        $this->assertFalse($status->isPropuesta());
        $this->assertTrue($status->isComunicadoSacd());
        $this->assertFalse($status->isComunicadoCtr());
    }

    public function test_isComunicadoCtr_returns_true_for_comunicado_ctr_status()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_COMUNICADO_CTR);
        $this->assertFalse($status->isPropuesta());
        $this->assertFalse($status->isComunicadoSacd());
        $this->assertTrue($status->isComunicadoCtr());
    }

    public function test_toString_returns_string_representation()
    {
        $status = new EncargoDiaStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $this->assertEquals('1', (string)$status);
    }
}
