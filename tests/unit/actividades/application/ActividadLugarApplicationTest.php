<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadLugar;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;

final class ActividadLugarApplicationTest extends TestCase
{
    public function test_get_filtro_lugar_con_dl(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getDl')->willReturn('dlb');
        $casa->method('getRegion')->willReturn('r1');

        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('findById')->with(5)->willReturn($casa);

        $o = new ActividadLugar($casaRepo, $this->createMock(CentroRepositoryInterface::class));
        $this->assertSame('dl|dlb', $o->getFiltroLugar(5));
    }

    public function test_get_filtro_lugar_sin_dl_usa_region(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getDl')->willReturn('');
        $casa->method('getRegion')->willReturn('Bel');

        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('findById')->willReturn($casa);

        $o = new ActividadLugar($casaRepo, $this->createMock(CentroRepositoryInterface::class));
        $this->assertSame('r|Bel', $o->getFiltroLugar(1));
    }

    public function test_get_lugares_posibles_vacio_si_entrada_vacia(): void
    {
        $o = new ActividadLugar(
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
        );
        $this->assertSame([], $o->getLugaresPosibles(''));
    }

    public function test_get_lugares_posibles_una_casa_y_centro(): void
    {
        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('getArrayCasas')->willReturn([10 => 'Casa A']);

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('getArrayCentrosCdc')->willReturn([20 => 'Ctr B']);

        $o = new ActividadLugar($casaRepo, $centroRepo);
        $o->setSsfsv('sv');
        $out = $o->getLugaresPosibles('dl|dlb');

        $this->assertSame([10 => 'Casa A', 20 => 'Ctr B'], $out);
    }
}
