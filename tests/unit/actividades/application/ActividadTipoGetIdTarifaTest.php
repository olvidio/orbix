<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;

final class ActividadTipoGetIdTarifaTest extends TestCase
{
    public function test_sin_resultados_devuelve_cadena_vacia(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getTipoActivTarifas')->willReturn([]);

        $this->assertSame('', (new ActividadTipoGetIdTarifa($repo))->execute(['entrada' => '123456']));
    }

    public function test_devuelve_id_tarifa_del_primer_elemento(): void
    {
        $rel = new class {
            public function getId_tarifa(): int
            {
                return 42;
            }
        };

        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getTipoActivTarifas')->willReturn([$rel]);

        $this->assertSame('42', (new ActividadTipoGetIdTarifa($repo))->execute(['entrada' => '123456']));
    }
}
