<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\planning\application\CasaPeriodosForPlanning;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CasaPeriodosForPlanningTest extends TestCase
{
    public function test_sin_claves_u_devuelve_mapa_vacio(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->never())->method('getArrayCasaPeriodos');

        $service = new CasaPeriodosForPlanning($repo);
        $ini = new DateTimeLocal('2025-01-01 10:00:00');
        $fin = new DateTimeLocal('2025-01-31 10:00:00');
        $this->assertSame([], $service->collect([['p#1#x' => []]], $ini, $fin));
    }

    public function test_extrae_id_ubi_y_consulta_periodos(): void
    {
        $ini = new DateTimeLocal('2025-01-01 10:00:00');
        $fin = new DateTimeLocal('2025-01-31 10:00:00');
        $periodos = [
            0 => [
                'iso_ini' => '2025-01-05',
                'iso_fin' => '2025-01-20',
                'sfsv' => 1,
            ],
        ];

        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayCasaPeriodos')
            ->with(42, $ini, $fin)
            ->willReturn($periodos);

        $service = new CasaPeriodosForPlanning($repo);
        $act = [
            'grp' => [
                'u#42#k' => [],
            ],
        ];

        $this->assertSame([42 => $periodos], $service->collect($act, $ini, $fin));
    }
}
