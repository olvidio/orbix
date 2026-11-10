<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use Tests\myTest;

/** Construcción de la cuadrícula de una zona: columnas por tipo de plantilla y preferencia de «última plantilla». */
final class CuadriculaZonaGridDataTest extends myTest
{
    private PreferenciaRepositoryInterface&MockObject $preferencias;
    private EncargoRepositoryInterface&MockObject $encargos;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['id_usuario' => 55];
        $this->preferencias = $this->createMock(PreferenciaRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargos->method('matching')->willReturn([]);
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    private function grid(): CuadriculaZonaGridData
    {
        return new CuadriculaZonaGridData(
            $this->preferencias,
            $this->createMock(ZonaSacdRepositoryInterface::class),
            $this->createMock(InicialesSacdService::class),
            $this->createMock(EncargoTipoRepositoryInterface::class),
            $this->createMock(EncargoDiaRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->encargos,
            $this->createMock(EncargoSacdHorarioRepositoryInterface::class),
            $this->createMock(EncargoHorarioRepositoryInterface::class)
        );
    }

    private function columnas(string $json): int
    {
        return substr_count($json, "'field'");
    }

    public function test_los_accesores_devuelven_lo_inyectado(): void
    {
        $g = $this->grid();

        $this->assertSame($this->preferencias, $g->getPreferenciaRepository());
        $this->assertSame($this->encargos, $g->getEncargoRepository());
        $this->assertInstanceOf(ZonaSacdRepositoryInterface::class, $g->getZonaSacdRepository());
        $this->assertInstanceOf(InicialesSacdService::class, $g->getInicialesSacdService());
        $this->assertInstanceOf(EncargoTipoRepositoryInterface::class, $g->getEncargoTipoRepository());
        $this->assertInstanceOf(EncargoDiaRepositoryInterface::class, $g->getEncargoDiaRepository());
        $this->assertInstanceOf(ActividadRepositoryInterface::class, $g->getActividadRepository());
        $this->assertInstanceOf(ActividadCargoRepositoryInterface::class, $g->getActividadCargoRepository());
        $this->assertInstanceOf(EncargoSacdHorarioRepositoryInterface::class, $g->getEncargoSacdHorarioRepository());
        $this->assertInstanceOf(EncargoHorarioRepositoryInterface::class, $g->getEncargoHorarioRepository());
    }

    /**
     * @dataProvider plantillasProvider
     */
    public function test_las_columnas_dependen_del_tipo_de_plantilla(string $tipo, int $columnas): void
    {
        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => $tipo, 'periodo' => 'otro', 'empiezamin' => '2026-03-02', 'empiezamax' => '2026-03-04']);

        $this->assertSame('', $r['error']);
        $this->assertSame($columnas, $this->columnas($r['columns_cuadricula']));
        // Sin encargos solo queda la fila de título de los sacerdotes de la zona.
        $this->assertLessThanOrEqual(1, count($r['data_cuadricula']));
        $this->assertSame(4, $r['id_zona']);
        $this->assertSame($tipo, $r['tipo_plantilla']);
        $this->assertSame('desc_enc', $r['orden'], 'orden por defecto');
        $this->assertNotSame('', $r['ctx_update']);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function plantillasProvider(): iterable
    {
        yield 'semanal' => ['s1', 1 + 7];
        yield 'domingos' => ['d1', 1 + 11];
        yield 'mensual' => ['m1', 1 + 35];
        yield 'plan de misas (rango dado)' => ['p', 1 + 3];
    }

    public function test_una_plantilla_guarda_la_ultima_usada_como_preferencia_del_usuario(): void
    {
        $this->preferencias->method('findById')->with(55, 'ultima_plantilla')->willReturn(null);
        $guardada = null;
        $this->preferencias->expects($this->once())->method('Guardar')->willReturnCallback(function (Preferencia $p) use (&$guardada): bool {
            $guardada = $p;

            return true;
        });

        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 's3']);

        $this->assertSame('', $r['preference_warning']);
        $this->assertSame(55, $guardada->getId_usuario());
        $this->assertSame('s3', $guardada->getPreferenciaVo()->value());
    }

    public function test_si_no_se_puede_guardar_la_preferencia_avisa_pero_sigue(): void
    {
        $this->preferencias->method('Guardar')->willReturn(false);
        $this->preferencias->method('getErrorTxt')->willReturn('sql mal');

        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 's1']);

        $this->assertStringContainsString('sql mal', $r['preference_warning']);
        $this->assertSame('', $r['error']);
    }

    public function test_el_plan_de_misas_no_toca_la_preferencia(): void
    {
        $this->preferencias->expects($this->never())->method('Guardar');

        $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 'p', 'periodo' => 'otro', 'empiezamin' => '2026-03-02', 'empiezamax' => '2026-03-02']);
    }

    public function test_un_fallo_de_runtime_devuelve_el_error_con_la_cuadricula_vacia(): void
    {
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargos->method('matching')->willThrowException(new \RuntimeException('sin encargos'));

        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 'p', 'periodo' => 'otro', 'empiezamin' => '2026-03-02', 'empiezamax' => '2026-03-03', 'fila' => '2', 'columna' => '3']);

        $this->assertSame('sin encargos', $r['error']);
        $this->assertSame('[]', $r['columns_cuadricula']);
        $this->assertSame([2, 3], [$r['fila'], $r['columna']]);
        $this->assertArrayHasKey('estados_opciones', $r);
    }

    public function test_si_el_fin_es_anterior_al_inicio_se_iguala_al_inicio(): void
    {
        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 'p', 'periodo' => 'otro', 'empiezamin' => '2026-03-05', 'empiezamax' => '2026-03-01']);

        $this->assertSame(1 + 1, $this->columnas($r['columns_cuadricula']));
    }

    /**
     * @dataProvider periodosProvider
     */
    public function test_los_periodos_relativos_generan_columnas_de_dias(string $periodo, int $minimo): void
    {
        $r = $this->grid()->build(['id_zona' => 4, 'tipo_plantilla' => 'p', 'periodo' => $periodo]);

        $this->assertSame('', $r['error']);
        $this->assertGreaterThanOrEqual(1 + $minimo, $this->columnas($r['columns_cuadricula']));
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function periodosProvider(): iterable
    {
        yield 'esta semana' => ['esta_semana', 7];
        yield 'próxima semana' => ['proxima_semana', 7];
        yield 'este mes' => ['este_mes', 28];
        yield 'próximo mes' => ['proximo_mes', 28];
    }
}
