<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\ResumenPlazasData;

/**
 * Solo la validacion temprana: evita instanciar DBPropiedades ni servicios.
 */
final class ResumenPlazasDataTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_sin_id_activ_devuelve_error_y_metricas_cero(): void
    {
        $GLOBALS['container'] = new class {
            public function get(string $id): never
            {
                throw new \RuntimeException('Unexpected DI: ' . $id);
            }
        };

        $out = ResumenPlazasData::execute(['id_activ' => 0]);
        $this->assertArrayHasKey('error', $out);
        $this->assertNotSame('', $out['error']);
        $this->assertSame(0, $out['id_activ']);
        $this->assertSame([], $out['a_plazas']);
        $this->assertSame(0, $out['plazas_totales']);
    }
}
