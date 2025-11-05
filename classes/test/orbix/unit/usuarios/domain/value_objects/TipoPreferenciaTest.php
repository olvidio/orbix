<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\TipoPreferencia;
use Tests\myTest;

class TipoPreferenciaTest extends myTest
{
    public function test_create_valid_tipo_preferencia()
    {
        $tipoPreferencia = new TipoPreferencia('theme');
        $this->assertEquals('theme', $tipoPreferencia->value());
    }

    public function test_empty_tipo_preferencia_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Preference type cannot be empty');
        new TipoPreferencia('');
    }

    public function test_equals_returns_true_for_same_tipo_preferencia()
    {
        $tipoPreferencia1 = new TipoPreferencia('theme');
        $tipoPreferencia2 = new TipoPreferencia('theme');
        $this->assertTrue($tipoPreferencia1->equals($tipoPreferencia2));
    }

    public function test_equals_returns_false_for_different_tipo_preferencia()
    {
        $tipoPreferencia1 = new TipoPreferencia('theme');
        $tipoPreferencia2 = new TipoPreferencia('language');
        $this->assertFalse($tipoPreferencia1->equals($tipoPreferencia2));
    }

    public function test_to_string_returns_tipo_preferencia_value()
    {
        $tipoPreferencia = new TipoPreferencia('theme');
        $this->assertEquals('theme', (string)$tipoPreferencia);
    }
}