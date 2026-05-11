<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CalendarioPeriodosGetData;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;
use src\shared\domain\value_objects\DateTimeLocal;

final class CalendarioPeriodosGetDataTest extends TestCase
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

    public function test_id_no_positivo_devuelve_array_vacio_sin_consultar_repo(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CasaPeriodoRepositoryInterface::class => $this->createMock(CasaPeriodoRepositoryInterface::class),
        ]);

        $this->assertSame([], CalendarioPeriodosGetData::execute(0));
        $this->assertSame([], CalendarioPeriodosGetData::execute(-3));
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

        $GLOBALS['container'] = $this->containerFromMap([
            CasaPeriodoRepositoryInterface::class => $repo,
        ]);

        $out = CalendarioPeriodosGetData::execute(7);

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

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }

                return $this->services[$id];
            }
        };
    }
}
