<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\planning\application\ActividadesPorCasasService;
use src\planning\application\CasaPeriodosForPlanning;
use src\planning\application\PlanningCasaVerData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class PlanningCasaVerDataTest extends TestCase
{
    private ActividadesPorCasasService&MockObject $actividades;
    private CasaPeriodoRepositoryInterface&MockObject $periodos;
    private PlanningCasaVerData $useCase;

    protected function setUp(): void
    {
        $this->actividades = $this->createMock(ActividadesPorCasasService::class);
        $this->periodos = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $this->useCase = new PlanningCasaVerData($this->actividades, new CasaPeriodosForPlanning($this->periodos));
    }

    /**
     * @return array<string, string>
     */
    private function fechas(): array
    {
        return ['f_ini_iso' => '2025-04-01', 'f_fin_iso' => '2025-06-30'];
    }

    /**
     * @dataProvider fechasIncompletasProvider
     * @param array<string, string> $input
     */
    public function test_sin_las_dos_fechas_lanza_excepcion(array $input): void
    {
        $this->actividades->expects($this->never())->method('actividadesPorCasas');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('f_ini_iso');
        $this->useCase->execute($input);
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function fechasIncompletasProvider(): iterable
    {
        yield 'ninguna' => [[]];
        yield 'falta el fin' => [['f_ini_iso' => '2025-04-01']];
        yield 'falta el inicio' => [['f_fin_iso' => '2025-06-30']];
        yield 'vacías' => [['f_ini_iso' => '', 'f_fin_iso' => '']];
    }

    public function test_pasa_al_servicio_el_tipo_de_casas_el_periodo_y_sin_actividad(): void
    {
        $this->actividades->expects($this->once())->method('actividadesPorCasas')->with(
            3,
            $this->callback(fn (DateTimeLocal $d) => $d->format('Y-m-d') === '2025-04-01'),
            $this->callback(fn (DateTimeLocal $d) => $d->format('Y-m-d') === '2025-06-30'),
            1,
            $this->callback(fn (DateTimeLocal $d) => $d->format('Y-m-d') === '2025-06-30'),
            $this->callback(fn (DateTimeLocal $d) => $d->format('Y-m-d') === '2025-04-01'),
            null
        )->willReturn(['', []]);

        $this->useCase->execute($this->fechas() + ['cdc_sel' => '3', 'sin_activ' => '1']);
    }

    public function test_cdc_sel_y_sin_activ_ausentes_valen_cero(): void
    {
        $this->actividades->expects($this->once())->method('actividadesPorCasas')
            ->with(0, $this->anything(), $this->anything(), 0, $this->anything(), $this->anything(), null)
            ->willReturn(['', []]);

        $this->useCase->execute($this->fechas());
    }

    public function test_con_cdc_sel_9_se_usan_las_casas_seleccionadas_sin_espacios_ni_vacios(): void
    {
        $this->actividades->expects($this->once())->method('actividadesPorCasas')
            ->with(9, $this->anything(), $this->anything(), 0, $this->anything(), $this->anything(), ['12', '34', '56'])
            ->willReturn(['', []]);

        $this->useCase->execute($this->fechas() + ['cdc_sel' => '9', 'sSeleccionados' => ' 12, 34 ,,56 ,']);
    }

    public function test_con_cdc_sel_9_y_seleccion_vacia_no_filtra_por_casas(): void
    {
        $this->actividades->expects($this->once())->method('actividadesPorCasas')
            ->with(9, $this->anything(), $this->anything(), 0, $this->anything(), $this->anything(), null)
            ->willReturn(['', []]);

        $this->useCase->execute($this->fechas() + ['cdc_sel' => '9', 'sSeleccionados' => '']);
    }

    public function test_la_seleccion_solo_cuenta_con_cdc_sel_9(): void
    {
        $this->actividades->expects($this->once())->method('actividadesPorCasas')
            ->with(2, $this->anything(), $this->anything(), 0, $this->anything(), $this->anything(), null)
            ->willReturn(['', []]);

        $this->useCase->execute($this->fechas() + ['cdc_sel' => '2', 'sSeleccionados' => '12,34']);
    }

    public function test_devuelve_las_actividades_y_los_periodos_de_cada_casa_una_sola_vez(): void
    {
        $actividades = [
            'Casa A' => ['u#12#x' => [['id' => 1]], 'u#12#y' => [['id' => 2]]],
            'Casa B' => ['u#34#z' => [['id' => 3]]],
        ];
        $this->actividades->method('actividadesPorCasas')->willReturn(['', $actividades]);
        $this->periodos->expects($this->exactly(2))->method('getArrayCasaPeriodos')
            ->willReturnCallback(static fn (int $id) => [[ 'iso_ini' => '2025-04-01', 'iso_fin' => '2025-04-30', 'sfsv' => $id]]);

        $out = $this->useCase->execute($this->fechas() + ['cdc_sel' => '1']);

        $this->assertSame($actividades, $out['a_actividades']);
        $this->assertSame([12, 34], array_keys($out['casa_periodos_por_ubi']));
        $this->assertSame(34, $out['casa_periodos_por_ubi'][34][0]['sfsv']);
    }

    public function test_sin_actividades_no_consulta_periodos(): void
    {
        $this->actividades->method('actividadesPorCasas')->willReturn(['', []]);
        $this->periodos->expects($this->never())->method('getArrayCasaPeriodos');

        $out = $this->useCase->execute($this->fechas());

        $this->assertSame(['a_actividades' => [], 'casa_periodos_por_ubi' => []], $out);
    }
}
