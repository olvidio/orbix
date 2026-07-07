<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application\legacy;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\application\legacy\Avisos;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use Tests\myTest;

final class AvisosCompararTest extends myTest
{
    private Avisos $avisos;

    public function setUp(): void
    {
        parent::setUp();

        $this->avisos = new Avisos(
            $this->createMock(CambioUsuarioRepositoryInterface::class),
            $this->createMock(CambioAnotadoRepositoryInterface::class),
            $this->createMock(UsuarioRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ZonaRepositoryInterface::class),
            $this->createMock(ZonaSacdRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class),
        );
    }

    public function test_comparar_fecha_iso_y_local_son_iguales(): void
    {
        $this->assertTrue($this->avisos->comparar('2026-07-07', '=', '07/07/2026'));
    }

    public function test_comparar_fechas_distintas_devuelve_false(): void
    {
        $this->assertFalse($this->avisos->comparar('2026-07-07', '=', '08/07/2026'));
    }

    public function test_comparar_hora_con_y_sin_segundos(): void
    {
        $this->assertTrue($this->avisos->comparar('10:30:00', '=', '10:30'));
    }
}
