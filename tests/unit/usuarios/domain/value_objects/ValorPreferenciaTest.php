<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\ValorPreferencia;
use Tests\myTest;

class ValorPreferenciaTest extends myTest
{
    public function test_create_valid_valor_preferencia()
    {
        $valorPreferencia = new ValorPreferencia('dark');
        $this->assertEquals('dark', $valorPreferencia->value());
    }

    public function test_create_empty_valor_preferencia()
    {
        // ValorPreferencia allows empty values, so this should not throw an exception
        $valorPreferencia = new ValorPreferencia('');
        $this->assertEquals('', $valorPreferencia->value());
    }

    public function test_equals_returns_true_for_same_valor_preferencia()
    {
        $valorPreferencia1 = new ValorPreferencia('dark');
        $valorPreferencia2 = new ValorPreferencia('dark');
        $this->assertTrue($valorPreferencia1->equals($valorPreferencia2));
    }

    public function test_equals_returns_false_for_different_valor_preferencia()
    {
        $valorPreferencia1 = new ValorPreferencia('dark');
        $valorPreferencia2 = new ValorPreferencia('light');
        $this->assertFalse($valorPreferencia1->equals($valorPreferencia2));
    }

    public function test_to_string_returns_valor_preferencia_value()
    {
        $valorPreferencia = new ValorPreferencia('dark');
        $this->assertEquals('dark', (string)$valorPreferencia);
    }
}