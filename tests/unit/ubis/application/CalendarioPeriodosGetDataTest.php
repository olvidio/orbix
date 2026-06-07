<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\CalendarioPeriodosGetData;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;

final class CalendarioPeriodosGetDataTest extends TestCase
{
    public function test_id_no_positivo_devuelve_array_vacio_sin_consultar_repo(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->never())->method('getCasaPeriodos');

        $useCase = new CalendarioPeriodosGetData($repo);

        $this->assertSame([], $useCase->execute(0));
        $this->assertSame([], $useCase->execute(-3));
    }

    public function test_mapea_periodos_del_repositorio(): void
    {
        $fIni = $this->createMock(DateTimeLocal::class);
        $fIni->method('getFromLocal')->willReturn('01/09/2024');
        $fFin = $this->createMock(DateTimeLocal::class);
        $fFin->method('getFromLocal')->willReturn('30/06/2025');

        $periodo = $this->createMock(CasaPeriodo::class);
        $periodo->method('getId_item')->willReturn(100);
        $periodo->method('getId_ubi')->willReturn(7);
        $periodo->method('getF_ini')->willReturn($fIni);
        $periodo->method('getF_fin')->willReturn($fFin);
        $periodo->method('getSfsv')->willReturn(1);

        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getCasaPeriodos')
            ->with(['id_ubi' => 7, '_ordre' => 'f_ini'])
            ->willReturn([$periodo]);

        $useCase = new CalendarioPeriodosGetData($repo);
        $out = $useCase->execute(7);

        $this->assertSame([
            [
                'id_item' => 100,
                'id_ubi' => 7,
                'f_ini' => '01/09/2024',
                'f_fin' => '30/06/2025',
                'sfsv' => 1,
            ],
        ], $out['rows']);
    }
}
