<?php

declare(strict_types=1);

namespace Tests\unit\inventario\infrastructure\ui\http;

use src\inventario\infrastructure\ui\http\RangoPeriodoRequest;
use Tests\myTest;

/**
 * Rango de fechas del formulario de periodo de «hacer equipajes» ({@see RangoPeriodoRequest}).
 */
final class RangoPeriodoRequestTest extends myTest
{
    /** Antes: «31/12/2027» iba tal cual a la consulta (error de PostgreSQL) y «1/2/2027» se leía como 2 de enero. */
    public function test_periodo_otro_convierte_las_fechas_locales_a_iso(): void
    {
        $this->assertSame(
            ['inicio' => '2027-02-01', 'fin' => '2027-12-31'],
            RangoPeriodoRequest::resolver(['periodo' => 'otro', 'year' => '2024', 'empiezamin' => '1/2/2027', 'empiezamax' => '31/12/2027'])
        );
        $this->assertSame(
            ['inicio' => '2027-03-05', 'fin' => '2027-03-06'],
            RangoPeriodoRequest::resolver(['inicio' => '5/3/2027', 'fin' => '6/3/2027']),
            'sin periodo, con inicio y fin'
        );
    }

    public function test_periodo_con_nombre_usa_el_anyo(): void
    {
        $this->assertSame(['inicio' => '2027-01-01', 'fin' => '2027-12-31'], RangoPeriodoRequest::resolver(['periodo' => 'tot_any', 'year' => '2027']));
    }

    public function test_sin_fechas_o_con_una_fecha_imposible_devuelve_el_error(): void
    {
        $this->assertSame('Faltan parámetros: periodo / empiezamin / empiezamax', RangoPeriodoRequest::resolver(['periodo' => 'otro', 'empiezamin' => '1/2/2027']));
        $this->assertSame('Fecha no válida: mañana - 1/3/2027', RangoPeriodoRequest::resolver(['periodo' => 'otro', 'empiezamin' => 'mañana', 'empiezamax' => '1/3/2027']));
    }
}
