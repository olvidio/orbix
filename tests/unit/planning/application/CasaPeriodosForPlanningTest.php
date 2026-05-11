<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\planning\application\CasaPeriodosForPlanning;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CasaPeriodosForPlanningTest extends TestCase
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

    public function test_sin_claves_u_devuelve_mapa_vacio(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->never())->method('getArrayCasaPeriodos');

        $GLOBALS['container'] = $this->containerFromMap([
            CasaPeriodoRepositoryInterface::class => $repo,
        ]);

        $ini = new DateTimeLocal('2025-01-01 10:00:00');
        $fin = new DateTimeLocal('2025-01-31 10:00:00');
        $this->assertSame([], CasaPeriodosForPlanning::collect([['p#1#x' => []]], $ini, $fin));
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

        $GLOBALS['container'] = $this->containerFromMap([
            CasaPeriodoRepositoryInterface::class => $repo,
        ]);

        $act = [
            'grp' => [
                'u#42#k' => [],
            ],
        ];

        $this->assertSame([42 => $periodos], CasaPeriodosForPlanning::collect($act, $ini, $fin));
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
