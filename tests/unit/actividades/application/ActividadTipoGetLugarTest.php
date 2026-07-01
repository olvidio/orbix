<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadLugar;
use src\actividades\application\ActividadTipoGetLugar;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

final class ActividadTipoGetLugarTest extends TestCase
{
    public function test_payload_usa_actividad_lugar(): void
    {
        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('getArrayCasas')->willReturn([1 => 'X']);

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('getArrayCentrosCdc')->willReturn([]);

        $lugar = new ActividadLugar($casaRepo, $centroRepo);
        $out = (new ActividadTipoGetLugar($lugar))->execute([
            'entrada' => 'dl|dlb',
            'isfsv' => 0,
            'ssfsv' => 'sv',
            'opcion_sel' => '1',
        ]);

        $this->assertSame('id_ubi', $out['id']);
        $this->assertTrue($out['blanco']);
        $this->assertSame('1', $out['selected']);
        $this->assertSame([['1', 'X']], $out['opciones']);
    }
}
