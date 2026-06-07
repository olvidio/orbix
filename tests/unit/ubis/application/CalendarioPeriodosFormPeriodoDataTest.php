<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;

/**
 * Datos del formulario de un periodo de calendario (JSON vía {@see CalendarioPeriodosFormPeriodoData::execute}).
 */
final class CalendarioPeriodosFormPeriodoDataTest extends TestCase
{
    public function test_execute_cumple_contrato_de_claves(): void
    {
        $useCase = new CalendarioPeriodosFormPeriodoData($this->repoConPeriodo($this->periodoStub(2, '2024-01-10', '2024-02-01')));
        $data = $useCase->execute(99);

        $this->assertSame(['id_item', 'f_ini', 'f_fin', 'sel_sv', 'sel_sf', 'sel_res'], array_keys($data));
    }

    public function test_execute_sv_marca_sel_sv(): void
    {
        $useCase = new CalendarioPeriodosFormPeriodoData($this->repoConPeriodo($this->periodoStub(1, 'a', 'b')));
        $data = $useCase->execute(1);

        $this->assertSame(1, $data['id_item']);
        $this->assertSame('a', $data['f_ini']);
        $this->assertSame('b', $data['f_fin']);
        $this->assertSame('selected', $data['sel_sv']);
        $this->assertSame('', $data['sel_sf']);
        $this->assertSame('', $data['sel_res']);
    }

    public function test_execute_sf_marca_sel_sf(): void
    {
        $useCase = new CalendarioPeriodosFormPeriodoData($this->repoConPeriodo($this->periodoStub(2, '', '')));
        $data = $useCase->execute(2);

        $this->assertSame('', $data['sel_sv']);
        $this->assertSame('selected', $data['sel_sf']);
        $this->assertSame('', $data['sel_res']);
    }

    public function test_execute_res_marca_sel_res(): void
    {
        $useCase = new CalendarioPeriodosFormPeriodoData($this->repoConPeriodo($this->periodoStub(3, '', '')));
        $data = $useCase->execute(3);

        $this->assertSame('', $data['sel_sv']);
        $this->assertSame('', $data['sel_sf']);
        $this->assertSame('selected', $data['sel_res']);
    }

    private function periodoStub(int $sfsv, string $fIni, string $fFin): CasaPeriodo
    {
        $fIniVo = $this->createMock(DateTimeLocal::class);
        $fIniVo->method('getFromLocal')->willReturn($fIni);
        $fFinVo = $this->createMock(DateTimeLocal::class);
        $fFinVo->method('getFromLocal')->willReturn($fFin);

        $periodo = $this->createMock(CasaPeriodo::class);
        $periodo->method('getF_ini')->willReturn($fIniVo);
        $periodo->method('getF_fin')->willReturn($fFinVo);
        $periodo->method('getSfsv')->willReturn($sfsv);

        return $periodo;
    }

    private function repoConPeriodo(CasaPeriodo $periodo): CasaPeriodoRepositoryInterface
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('findById')->willReturn($periodo);

        return $repo;
    }
}
