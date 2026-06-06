<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\ResumenPlazasData;
use src\actividadplazas\application\services\ResumenPlazasService;

/**
 * Solo la validacion temprana: evita instanciar DBPropiedades ni servicios.
 */
final class ResumenPlazasDataTest extends TestCase
{
    public function test_sin_id_activ_devuelve_error_y_metricas_cero(): void
    {
        $useCase = new ResumenPlazasData(
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ResumenPlazasService::class),
        );

        $out = $useCase->execute(['id_activ' => 0]);
        $this->assertArrayHasKey('error', $out);
        $this->assertNotSame('', $out['error']);
        $this->assertSame(0, $out['id_activ']);
        $this->assertSame([], $out['a_plazas']);
        $this->assertSame(0, $out['plazas_totales']);
    }
}
