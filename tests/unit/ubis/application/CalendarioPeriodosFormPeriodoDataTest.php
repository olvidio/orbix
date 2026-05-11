<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

/**
 * Datos del formulario de un periodo de calendario (JSON vía {@see CalendarioPeriodosFormPeriodoData::execute}).
 */
final class CalendarioPeriodosFormPeriodoDataTest extends TestCase
{
    private mixed $previousContainer = null;

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

    public function test_execute_cumple_contrato_de_claves(): void
    {
        $GLOBALS['container'] = $this->containerConPeriodo($this->periodoStub(2, '2024-01-10', '2024-02-01'));
        $data = CalendarioPeriodosFormPeriodoData::execute(99);

        $this->assertSame(['id_item', 'f_ini', 'f_fin', 'sel_sv', 'sel_sf', 'sel_res'], array_keys($data));
    }

    public function test_execute_sv_marca_sel_sv(): void
    {
        $GLOBALS['container'] = $this->containerConPeriodo($this->periodoStub(1, 'a', 'b'));
        $data = CalendarioPeriodosFormPeriodoData::execute(1);

        $this->assertSame(1, $data['id_item']);
        $this->assertSame('a', $data['f_ini']);
        $this->assertSame('b', $data['f_fin']);
        $this->assertSame('selected', $data['sel_sv']);
        $this->assertSame('', $data['sel_sf']);
        $this->assertSame('', $data['sel_res']);
    }

    public function test_execute_sf_marca_sel_sf(): void
    {
        $GLOBALS['container'] = $this->containerConPeriodo($this->periodoStub(2, '', ''));
        $data = CalendarioPeriodosFormPeriodoData::execute(2);

        $this->assertSame('', $data['sel_sv']);
        $this->assertSame('selected', $data['sel_sf']);
        $this->assertSame('', $data['sel_res']);
    }

    public function test_execute_res_marca_sel_res(): void
    {
        $GLOBALS['container'] = $this->containerConPeriodo($this->periodoStub(3, '', ''));
        $data = CalendarioPeriodosFormPeriodoData::execute(3);

        $this->assertSame('', $data['sel_sv']);
        $this->assertSame('', $data['sel_sf']);
        $this->assertSame('selected', $data['sel_res']);
    }

    private function periodoStub(int $sfsv, string $fIni, string $fFin): object
    {
        return new class($sfsv, $fIni, $fFin) {
            public function __construct(
                private readonly int $sfsv,
                private readonly string $fIni,
                private readonly string $fFin,
            ) {}

            public function getF_ini(): object
            {
                $ini = $this->fIni;
                return new class($ini) {
                    public function __construct(private readonly string $v) {}
                    public function getFromLocal(): string
                    {
                        return $this->v;
                    }
                };
            }

            public function getF_fin(): object
            {
                $fin = $this->fFin;
                return new class($fin) {
                    public function __construct(private readonly string $v) {}
                    public function getFromLocal(): string
                    {
                        return $this->v;
                    }
                };
            }

            public function getSfsv(): int
            {
                return $this->sfsv;
            }
        };
    }

    private function containerConPeriodo(object $periodo): object
    {
        return new class($periodo) {
            public function __construct(private readonly object $periodo) {}

            public function get(string $key): object
            {
                if ($key !== CasaPeriodoRepositoryInterface::class) {
                    throw new \RuntimeException("Clave inesperada: $key");
                }
                return new class($this->periodo) {
                    public function __construct(private readonly object $periodo) {}

                    public function findById(int $id): object
                    {
                        return $this->periodo;
                    }
                };
            }
        };
    }
}
