<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\copias;

use PHPUnit\Framework\TestCase;
use src\shared\domain\copias\ValorCopia;

final class ValorCopiaTest extends TestCase
{
    // --- esVerdadero() ---------------------------------------------------

    public function test_es_verdadero_con_valores_verdaderos(): void
    {
        $this->assertTrue(ValorCopia::esVerdadero(true));
        $this->assertTrue(ValorCopia::esVerdadero('t'));
        $this->assertTrue(ValorCopia::esVerdadero('true'));
        $this->assertTrue(ValorCopia::esVerdadero('1'));
        $this->assertTrue(ValorCopia::esVerdadero(1));
        $this->assertTrue(ValorCopia::esVerdadero('SI'));
    }

    public function test_es_verdadero_con_valores_falsos(): void
    {
        $this->assertFalse(ValorCopia::esVerdadero(false));
        $this->assertFalse(ValorCopia::esVerdadero('f'));
        $this->assertFalse(ValorCopia::esVerdadero(''));
        $this->assertFalse(ValorCopia::esVerdadero(null));
        $this->assertFalse(ValorCopia::esVerdadero(0));
        $this->assertFalse(ValorCopia::esVerdadero(2));
    }

    // --- aTexto() --------------------------------------------------------

    public function test_null_y_cadena_vacia_dan_el_mismo_texto(): void
    {
        $this->assertSame('', ValorCopia::aTexto(null));
        $this->assertSame('', ValorCopia::aTexto(''));
    }

    public function test_los_booleanos_se_representan_como_en_postgres(): void
    {
        $this->assertSame('t', ValorCopia::aTexto(true));
        $this->assertSame('f', ValorCopia::aTexto(false));
    }

    public function test_los_numeros_y_su_forma_en_cadena_coinciden(): void
    {
        $this->assertSame(ValorCopia::aTexto(3), ValorCopia::aTexto('3'));
        $this->assertSame('3', ValorCopia::aTexto(3));
    }

    public function test_se_recorta_el_espacio_alrededor(): void
    {
        // Postgres devuelve char(n) rellenado con espacios; el origen no.
        $this->assertSame(ValorCopia::aTexto('dlb'), ValorCopia::aTexto('  dlb  '));
    }

    public function test_los_valores_no_escalares_se_serializan(): void
    {
        $this->assertSame('{"a":1}', ValorCopia::aTexto(['a' => 1]));
    }
}
