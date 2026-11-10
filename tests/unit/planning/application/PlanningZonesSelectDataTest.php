<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\planning\application\ActividadesPorZonasService;
use src\planning\application\PlanningZonesSelectData;
use src\shared\domain\value_objects\DateTimeLocal;

final class PlanningZonesSelectDataTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function respuestaDelServicio(): array
    {
        return [
            'actividades_por_zona' => [1 => ['a' => 1]],
            'cabeceras_por_zona' => [1 => 'Zona 1'],
            'zonas' => 1,
            'titulo' => 'Planning',
            'oIniPlanning' => new DateTimeLocal('2025-04-01'),
            'oFinPlanning' => new DateTimeLocal('2025-06-30'),
        ];
    }

    public function test_pasa_los_parametros_tipados_al_servicio(): void
    {
        $servicio = $this->createMock(ActividadesPorZonasService::class);
        $servicio->expects($this->once())->method('execute')
            ->with('3,5', 2, 2025, 'sg', 'p', null)
            ->willReturn($this->respuestaDelServicio());

        (new PlanningZonesSelectData($servicio))->execute([
            'id_zona' => '3,5', 'trimestre' => '2', 'year' => '2025', 'actividad' => 'sg', 'propuesta' => 'p',
        ]);
    }

    public function test_parametros_ausentes_llegan_como_vacio_o_cero(): void
    {
        $servicio = $this->createMock(ActividadesPorZonasService::class);
        $servicio->expects($this->once())->method('execute')->with('', 0, 0, '', '', null)
            ->willReturn($this->respuestaDelServicio());

        (new PlanningZonesSelectData($servicio))->execute([]);
    }

    public function test_sustituye_las_fechas_por_texto_iso_y_conserva_el_resto(): void
    {
        $servicio = $this->createMock(ActividadesPorZonasService::class);
        $servicio->method('execute')->willReturn($this->respuestaDelServicio());

        $out = (new PlanningZonesSelectData($servicio))->execute([]);

        $this->assertSame('2025-04-01', $out['planning_ini_iso']);
        $this->assertSame('2025-06-30', $out['planning_fin_iso']);
        $this->assertArrayNotHasKey('oIniPlanning', $out, 'los objetos de fecha no viajan en el JSON');
        $this->assertArrayNotHasKey('oFinPlanning', $out);
        $this->assertSame([1 => ['a' => 1]], $out['actividades_por_zona']);
        $this->assertSame([1 => 'Zona 1'], $out['cabeceras_por_zona']);
        $this->assertSame(1, $out['zonas']);
        $this->assertSame('Planning', $out['titulo']);
    }

    public function test_el_resultado_es_serializable_a_json(): void
    {
        $servicio = $this->createMock(ActividadesPorZonasService::class);
        $servicio->method('execute')->willReturn($this->respuestaDelServicio());

        $out = (new PlanningZonesSelectData($servicio))->execute([]);

        $this->assertNotFalse(json_encode($out, JSON_THROW_ON_ERROR));
    }
}
