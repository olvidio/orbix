<?php

declare(strict_types=1);

namespace Tests\unit\devel\application;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;

final class VerificarEstadoRenombrarEsquemaTest extends TestCase
{
    public function test_expresionDefaultEquivale_acepta_public_calificado_vs_pg_get_expr(): void
    {
        $verifier = new VerificarEstadoRenombrarEsquema();
        $method = new ReflectionMethod(VerificarEstadoRenombrarEsquema::class, 'expresionDefaultEquivale');
        $method->setAccessible(true);

        $db = "idschema('V-crV'::text)";
        $expected = "public.idschema('V-crV'::text)";

        $this->assertTrue($method->invoke($verifier, $db, $expected));
    }

    public function test_expresionDefaultEquivale_rechaza_valor_distinto(): void
    {
        $verifier = new VerificarEstadoRenombrarEsquema();
        $method = new ReflectionMethod(VerificarEstadoRenombrarEsquema::class, 'expresionDefaultEquivale');
        $method->setAccessible(true);

        $db = "idschema('B-crB'::text)";
        $expected = "public.idschema('V-crV'::text)";

        $this->assertFalse($method->invoke($verifier, $db, $expected));
    }
}
