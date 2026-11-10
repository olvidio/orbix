<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ActividadesBuscarData;
use src\notas\application\ListadoAnualActasData;
use src\notas\application\PosiblesPreceptoresData;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\profesores\domain\services\ProfesorStgrService;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

final class NotasConsultasPequenasTest extends myTest
{
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function conAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, null, null, null, ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8], null);
    }

    // ---- PosiblesPreceptoresData ----------------------------------------------------------------------

    public function test_los_posibles_preceptores_son_los_profesores_de_la_dl(): void
    {
        $servicio = $this->createMock(ProfesorStgrService::class);
        $servicio->expects($this->once())->method('getArrayProfesoresDl')->willReturn([10011 => 'Perez, A', 10012 => 'Gil, B']);

        $this->assertSame([10011 => 'Perez, A', 10012 => 'Gil, B'], (new PosiblesPreceptoresData($servicio))->execute());
    }

    // ---- ActividadesBuscarData -------------------------------------------------------------------------------

    private function actividad(int $id, string $nombre): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ($nombre);

        return $a;
    }

    private function buscar(ActividadRepositoryInterface $repo): ActividadesBuscarData
    {
        return new ActividadesBuscarData($repo, new DelegacionDropdown($this->createMock(DelegacionRepositoryInterface::class)));
    }

    public function test_con_fecha_de_acta_busca_tres_meses_antes_y_despues(): void
    {
        $repo = $this->createMock(ActividadRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $out = $this->buscar($repo)->execute(['f_acta_iso' => '2025-06-15', 'dl_org' => 'dlp']);

        $this->assertSame('2025-03-15', $out['f_ini_iso']);
        $this->assertSame('2025-09-15', $out['f_fin_iso']);
        $this->assertSame('dlp', $out['dl_org_sel']);
    }

    public function test_sin_fecha_busca_desde_diez_meses_antes_hasta_un_mes_despues_de_hoy(): void
    {
        $repo = $this->createMock(ActividadRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $out = $this->buscar($repo)->execute([]);

        $this->assertSame((new \DateTime('today'))->sub(new \DateInterval('P10M'))->format('Y-m-d'), $out['f_ini_iso']);
        $this->assertSame((new \DateTime('today'))->add(new \DateInterval('P1M'))->format('Y-m-d'), $out['f_fin_iso']);
    }

    public function test_sin_dl_usa_la_propia(): void
    {
        $repo = $this->createMock(ActividadRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $this->assertSame(ConfigGlobal::mi_delef(), $this->buscar($repo)->execute([])['dl_org_sel']);
    }

    public function test_filtra_por_periodo_tipo_de_actividad_de_estudios_y_dl_ordenadas_por_fecha(): void
    {
        $repo = $this->createMock(ActividadRepositoryInterface::class);
        $repo->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(
                ['f_ini between [2025-03-15,2025-09-15]', 'id_tipo_activ matches ^1(12|33)', 'dl_org eq dlp'],
                DescribeCriteria::filtros($c)
            );
            $this->assertSame(['f_ini asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([]);

        $this->buscar($repo)->execute(['f_acta_iso' => '2025-06-15', 'dl_org' => 'dlp']);
    }

    public function test_devuelve_las_actividades_por_id_con_su_nombre_y_la_seleccionada(): void
    {
        $repo = $this->createMock(ActividadRepositoryInterface::class);
        $repo->method('matching')->willReturn([$this->actividad(30, 'Curso A'), $this->actividad(10, 'Curso B')]);

        $out = $this->buscar($repo)->execute(['id_activ' => '10', 'dl_org' => 'dlp']);

        $this->assertSame([30 => 'Curso A', 10 => 'Curso B'], $out['actividades']);
        $this->assertSame(10, $out['id_activ_sel']);
        $this->assertSame(['delegaciones', 'actividades', 'dl_org_sel', 'id_activ_sel', 'f_ini_iso', 'f_fin_iso'], array_keys($out));
    }

    // ---- ListadoAnualActasData ---------------------------------------------------------------------------------

    private function acta(string $acta, ?int $idAsignatura, ?string $fecha): Acta
    {
        if ($idAsignatura === null) {
            // Acta::setId_asignatura no admite null (el value object lo rechaza): se simula el valor leído.
            $m = $this->createMock(Acta::class);
            $m->method('getId_asignatura')->willReturn(null);
            $m->method('getActa')->willReturn($acta);

            return $m;
        }
        $a = new Acta();
        $a->setActa($acta);
        $a->setId_asignatura($idAsignatura);
        $a->setF_acta($fecha === null ? null : new DateTimeLocal($fecha));

        return $a;
    }

    private function asignatura(int $nivel, ?string $nombreCorto): Asignatura
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_nivel')->willReturn($nivel);
        $a->method('getNombre_corto')->willReturn($nombreCorto);

        return $a;
    }

    /**
     * @param list<Acta> $actas
     * @param array<int, Asignatura|null> $asignaturas
     */
    private function listado(array $actas, array $asignaturas, ?ActaRepositoryInterface &$repoRstgr = null, ?ActaDlRepositoryInterface &$repoDl = null): ListadoAnualActasData
    {
        $repoRstgr = $this->createMock(ActaRepositoryInterface::class);
        $repoRstgr->method('matchingSinPdf')->willReturn($actas);
        $repoDl = $this->createMock(ActaDlRepositoryInterface::class);
        $repoDl->method('matchingSinPdf')->willReturn($actas);
        $asig = $this->createMock(AsignaturaRepositoryInterface::class);
        $asig->method('findById')->willReturnCallback(static fn (int $id) => $asignaturas[$id] ?? null);
        $dele = $this->createMock(DelegacionRepositoryInterface::class);
        $dele->method('getArrayDlRegionStgr')->willReturn([1 => 'dlb', 2 => 'dlz']);

        return new ListadoAnualActasData($dele, $repoRstgr, $repoDl, $asig);
    }

    /**
     * Regresión: ordenar las fechas ISO como números las dejaba empatadas (solo cuenta el año) y el orden
     * dentro de un mismo nivel era el del repositorio.
     */
    public function test_el_listado_sale_ordenado_por_nivel_y_despues_por_fecha(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado(
            [
                $this->acta('dlb 3/24', 1103, '2024-06-20'),
                $this->acta('dlb 1/24', 1101, '2024-05-10'),
                $this->acta('dlb 2/24', 1101, '2024-03-01'),
            ],
            [1101 => $this->asignatura(1101, 'Lógica'), 1103 => $this->asignatura(1103, 'Ética')]
        );

        $r = $uc->execute('2024-01-01', '2024-12-31');

        $this->assertSame(['dlb 2/24', 'dlb 1/24', 'dlb 3/24'], array_column($r, 'acta'));
        $this->assertSame([1101, 1101, 1103], array_column($r, 'id_nivel'));
        $this->assertSame(['Lógica', 'Lógica', 'Ética'], array_column($r, 'nombre_corto'));
    }

    public function test_cada_fila_trae_la_fecha_en_formato_local(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([$this->acta('dlb 1/24', 1101, '2024-05-10')], [1101 => $this->asignatura(1101, 'Lógica')]);

        $this->assertSame((new DateTimeLocal('2024-05-10'))->getFromLocal(), $uc->execute('2024-01-01', '2024-12-31')[0]['f_acta']);
    }

    public function test_una_asignatura_sin_nombre_corto_sale_con_interrogantes_y_nivel_cero(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([$this->acta('dlb 1/24', 1101, '2024-05-10')], [1101 => $this->asignatura(1101, null)]);

        $fila = $uc->execute('2024-01-01', '2024-12-31')[0];

        $this->assertSame('???', $fila['nombre_corto']);
        $this->assertSame(0, $fila['id_nivel']);
    }

    public function test_un_acta_sin_asignatura_se_omite(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([$this->acta('dlb 1/24', null, '2024-05-10')], []);

        $this->assertSame([], $uc->execute('2024-01-01', '2024-12-31'));
    }

    public function test_una_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([$this->acta('dlb 1/24', 1101, '2024-05-10')], []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('1101');
        $uc->execute('2024-01-01', '2024-12-31');
    }

    public function test_un_acta_sin_fecha_se_lista_con_fecha_vacia(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([$this->acta('dlb 1/24', 1101, null)], [1101 => $this->asignatura(1101, 'Lógica')]);

        $this->assertSame('', $uc->execute('2024-01-01', '2024-12-31')[0]['f_acta']);
    }

    public function test_sin_actas_devuelve_lista_vacia(): void
    {
        $this->conAmbito('dl');

        $this->assertSame([], $this->listado([], [])->execute('2024-01-01', '2024-12-31'));
    }

    public function test_un_ambito_de_dl_usa_el_repositorio_de_la_dl_y_filtra_por_fecha(): void
    {
        $this->conAmbito('dl');
        $uc = $this->listado([], [], $repoRstgr, $repoDl);
        $repoRstgr->expects($this->never())->method('matchingSinPdf');
        $repoDl->expects($this->once())->method('matchingSinPdf')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['f_acta between [2024-01-01,2024-12-31]'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $uc->execute('2024-01-01', '2024-12-31');
    }

    public function test_un_ambito_de_region_stgr_usa_el_repositorio_comun_y_filtra_por_prefijos_de_sus_dl(): void
    {
        $this->conAmbito('rstgr');
        $uc = $this->listado([], [], $repoRstgr, $repoDl);
        $repoDl->expects($this->never())->method('matchingSinPdf');
        $repoRstgr->expects($this->once())->method('matchingSinPdf')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(
                ['f_acta between [2024-01-01,2024-12-31]', 'acta matches ^(dlb |dlz )'],
                DescribeCriteria::filtros($c)
            );

            return true;
        }))->willReturn([]);

        $uc->execute('2024-01-01', '2024-12-31');
    }
}
