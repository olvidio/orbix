<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadNuevoCurso;
use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\support\DescribeCriteria;

/**
 * Crear las actividades del curso nuevo a partir de las del curso de referencia:
 * desplazamiento de fechas por tipo de repetición, copia de campos, centros
 * encargados y fases; borrado previo del periodo y comprobación de solapes.
 */
final class ActividadNuevoCursoTest extends TestCase
{
    private ActividadDlRepositoryInterface&MockObject $actividades;
    private RepeticionRepositoryInterface&MockObject $repeticiones;
    private CentroEncargadoRepositoryInterface&MockObject $centros;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    private ?bool $registrarCambios = null;

    protected function setUp(): void
    {
        foreach (['config', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv', 'idioma' => 'es_ES.UTF-8'];
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->actividades = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->repeticiones = $this->createMock(RepeticionRepositoryInterface::class);
        $this->centros = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $this->repeticiones->method('matching')->willReturn([
            $this->repeticion(10, 0),
            $this->repeticion(11, 1),
            $this->repeticion(12, 2),
            $this->repeticion(13, 3),
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
    }

    private function repeticion(int $id, int $tipo): Repeticion
    {
        $r = new Repeticion();
        $r->setId_repeticion($id);
        $r->setTipoRepeticion($tipo);
        return $r;
    }

    /** @param list<string> $nombres */
    private function apps(array $nombres): void
    {
        $ids = ['procesos' => 3, 'actividadescentro' => 5];
        $a = array_intersect_key($ids, array_flip($nombres));
        $_SESSION['config'] = ['a_apps' => $a, 'app_installed' => array_values($a)];
    }

    private function uc(int $ref = 2026, int $year = 2027): ActividadNuevoCurso
    {
        $uc = new ActividadNuevoCurso($this->repeticiones, $this->actividades, $this->centros, $this->procesos);
        return $uc->setYear_ref($ref)->setYear($year);
    }

    private function origen(?int $idRepeticion, string $ini = '2026-09-07', string $fin = '2026-09-13', string $nom = 'cv sr (7/9/2026-13/9/2026) Pozoalbero'): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_auto(5);
        $a->setId_activ(3005);
        $a->setId_tipo_activ(112021);
        $a->setDl_org('dlb');
        $a->setNom_activ($nom);
        $a->setId_ubi(200);
        $a->setDesc_activ('desc');
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setH_ini(TimeLocal::fromString('20:30:00'));
        $a->setH_fin(TimeLocal::fromString('10:00:00'));
        $a->setPrecio(120.5);
        $a->setNum_asistentes(30);
        $a->setStatus(StatusId::ACTUAL);
        $a->setObserv('obs');
        $a->setNivel_stgr(2);
        $a->setId_repeticion($idRepeticion);
        $a->setObserv_material('material');
        $a->setLugar_esp('lugar');
        $a->setTarifa(7);
        return $a;
    }

    /** Captura la actividad que se guarda. */
    private function capturarGuardado(?ActividadAll &$guardada): void
    {
        $this->actividades->method('getNewId')->willReturn(77);
        $this->actividades->method('getNewIdActividad')->with(77)->willReturn(3077);
        $this->actividades->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (ActividadAll $a, bool $registrar) use (&$guardada): bool {
                $guardada = $a;
                $this->registrarCambios = $registrar;
                return true;
            });
    }

    public function test_sin_repeticion_avisa_y_no_guarda(): void
    {
        $this->actividades->expects($this->never())->method('Guardar');
        $txt = $this->uc()->crear_actividad($this->origen(null));
        $this->assertStringContainsString('no tiene definida la repetición', $txt);
        $this->assertStringContainsString('cv sr (7/9/2026-13/9/2026) Pozoalbero', $txt);
    }

    public function test_si_no_se_guarda_la_actividad_se_avisa_sin_cortar_la_peticion(): void
    {
        $this->actividades->method('getNewId')->willReturn(9001);
        $this->actividades->expects($this->once())->method('Guardar')->willReturn(false);
        $uc = $this->uc();
        $txt = $uc->crear_actividad($this->origen(11));
        $this->assertStringContainsString('ERROR: no se ha guardado la actividad', $txt);
        $this->assertStringContainsString('Pozoalbero', $txt);
        $this->assertSame(0, $uc->getNumCreadas());
    }

    public function test_tipo_no_se_repite_no_crea_nada(): void
    {
        $this->actividades->expects($this->never())->method('Guardar');
        $uc = $this->uc();
        $this->assertSame('', $uc->crear_actividad($this->origen(10)));
        $this->assertSame(0, $uc->getNumCreadas());
    }

    public function test_repeticion_inexistente_no_crea_nada(): void
    {
        $this->actividades->expects($this->never())->method('Guardar');
        $this->assertSame('', $this->uc()->crear_actividad($this->origen(99)));
    }

    public function test_por_dia_de_la_semana_suma_semanas_enteras_y_copia_los_campos(): void
    {
        $this->capturarGuardado($g);
        $uc = $this->uc()->setRegistrarCambios(false);
        $this->assertSame('', $uc->crear_actividad($this->origen(11)));
        $this->assertSame(1, $uc->getNumCreadas());
        $this->assertFalse($this->registrarCambios);

        // 364 días: el lunes 7/9/2026 pasa al lunes 6/9/2027.
        $this->assertSame('2027-09-06', $g->getF_ini()->format('Y-m-d'));
        $this->assertSame('2027-09-12', $g->getF_fin()->format('Y-m-d'));
        $this->assertSame($g->getF_ini()->format('N'), '1');
        $this->assertSame('cv sr (6/9/2027-12/9/2027) Pozoalbero', $g->getNom_activ());
        $this->assertSame(77, $g->getId_auto());
        $this->assertSame(3077, $g->getId_activ());
        $this->assertSame('dl', $g->getId_tabla());
        $this->assertSame(StatusId::PROYECTO, $g->getStatus());
        $this->assertSame(112021, $g->getId_tipo_activ());
        $this->assertSame('dlb', $g->getDl_org());
        $this->assertSame(200, $g->getId_ubi());
        $this->assertSame('desc', $g->getDesc_activ());
        $this->assertSame(120.5, $g->getPrecio());
        $this->assertSame(30, $g->getNum_asistentes());
        $this->assertSame('obs', $g->getObserv());
        $this->assertSame(2, $g->getNivel_stgr());
        $this->assertSame(11, $g->getId_repeticion());
        $this->assertSame('material', $g->getObserv_material());
        $this->assertSame('lugar', $g->getLugar_esp());
        $this->assertSame(7, $g->getTarifa());
        $this->assertSame('20:30:00', $g->getH_ini()->toDatabaseString());
        $this->assertSame('10:00:00', $g->getH_fin()->toDatabaseString());
    }

    public function test_por_dia_de_la_semana_con_dos_anios_de_diferencia(): void
    {
        $this->capturarGuardado($g);
        $this->uc(2025, 2027)->crear_actividad($this->origen(11, '2025-09-08', '2025-09-14'));
        $this->assertSame('2027-09-06', $g->getF_ini()->format('Y-m-d'));
    }

    public function test_por_dia_del_anio_conserva_la_fecha(): void
    {
        $this->capturarGuardado($g);
        $this->uc()->crear_actividad($this->origen(12, '2026-12-24', '2026-12-26', 'navidad (24/12/2026-26/12/2026)'));
        $this->assertSame('2027-12-24', $g->getF_ini()->format('Y-m-d'));
        $this->assertSame('2027-12-26', $g->getF_fin()->format('Y-m-d'));
        $this->assertSame('navidad (24/12/2027-26/12/2027)', $g->getNom_activ());
    }

    public function test_por_semana_santa_sigue_al_domingo_de_pascua(): void
    {
        // Pascua 2026: 5 de abril; 2027: 28 de marzo (357 días después).
        $this->capturarGuardado($g);
        $this->uc()->crear_actividad($this->origen(13, '2026-04-02', '2026-04-05', 'cv ss (2/4/2026-5/4/2026)'));
        $this->assertSame('2027-03-25', $g->getF_ini()->format('Y-m-d'));
        $this->assertSame('2027-03-28', $g->getF_fin()->format('Y-m-d'));
        $this->assertSame('cv ss (25/3/2027-28/3/2027)', $g->getNom_activ());
    }

    /**
     * Antes se sumaba el intervalo del diff (11 meses y 23 días): el martes santo
     * 31/3/2026 pasaba al viernes 26/3/2027 en vez del martes 23/3/2027.
     */
    public function test_por_semana_santa_conserva_el_dia_de_la_semana_aunque_cambie_el_mes(): void
    {
        $this->capturarGuardado($g);
        $this->uc()->crear_actividad($this->origen(13, '2026-03-31', '2026-04-05'));
        $this->assertSame('2027-03-23 Tue', $g->getF_ini()->format('Y-m-d D'));
        $this->assertSame('2027-03-28 Sun', $g->getF_fin()->format('Y-m-d D'));
    }

    public function test_por_semana_santa_hacia_un_anio_con_la_pascua_mas_tarde(): void
    {
        // Pascua 2027: 28 de marzo; 2028: 16 de abril.
        $this->capturarGuardado($g);
        $this->uc(2027, 2028)->crear_actividad($this->origen(13, '2027-03-23', '2027-03-28'));
        $this->assertSame('2028-04-11 Tue', $g->getF_ini()->format('Y-m-d D'));
        $this->assertSame('2028-04-16 Sun', $g->getF_fin()->format('Y-m-d D'));
    }

    public function test_nombre_sin_fechas_entre_parentesis_se_conserva(): void
    {
        $this->capturarGuardado($g);
        $this->uc()->crear_actividad($this->origen(12, nom: 'convivencia de verano'));
        $this->assertSame('convivencia de verano', $g->getNom_activ());
    }

    public function test_ver_lista_apunta_cada_actividad_y_se_consume(): void
    {
        $this->capturarGuardado($g);
        $uc = $this->uc()->setVer_lista(true);
        $uc->crear_actividad($this->origen(12));
        $this->assertSame('2=> 7/9/2027-13/9/2027 :: cv sr (7/9/2027-13/9/2027) Pozoalbero<br>', $uc->consumirListaHtml());
        $this->assertSame('', $uc->consumirListaHtml());
    }

    public function test_sin_ver_lista_no_apunta_nada(): void
    {
        $this->capturarGuardado($g);
        $uc = $this->uc();
        $uc->crear_actividad($this->origen(12));
        $this->assertSame('', $uc->consumirListaHtml());
    }

    public function test_sin_apps_no_copia_centros_ni_crea_fases(): void
    {
        $this->capturarGuardado($g);
        $this->centros->expects($this->never())->method('matching');
        $this->procesos->expects($this->never())->method('generarProceso');
        $this->uc()->crear_actividad($this->origen(12));
    }

    public function test_con_actividadescentro_copia_los_centros_a_la_actividad_nueva(): void
    {
        $this->apps(['actividadescentro']);
        $this->capturarGuardado($g);
        $c1 = new CentroEncargado();
        $c1->setId_activ(3005);
        $c1->setId_ubi(501);
        $c1->setNum_orden(1);
        $c2 = new CentroEncargado();
        $c2->setId_activ(3005);
        $c2->setId_ubi(502);
        $c2->setNum_orden(2);
        $this->centros->expects($this->once())->method('matching')
            ->willReturnCallback(function ($c) use ($c1, $c2): array {
                $this->assertSame(['id_activ eq 3005'], DescribeCriteria::filtros($c));
                return [$c1, $c2];
            });
        $guardados = [];
        $this->centros->expects($this->exactly(2))->method('Guardar')
            ->willReturnCallback(function (CentroEncargado $c) use (&$guardados): bool {
                $guardados[] = [$c->getId_activ(), $c->getId_ubi(), $c->getNum_orden()];
                return true;
            });
        $this->uc()->crear_actividad($this->origen(12));
        $this->assertSame([[3077, 501, '1'], [3077, 502, '2']], $guardados);
        // Los originales no se tocan.
        $this->assertSame(3005, $c1->getId_activ());
    }

    public function test_con_procesos_genera_las_fases_y_recoge_los_avisos(): void
    {
        $this->apps(['procesos']);
        $this->capturarGuardado($g);
        $this->procesos->expects($this->once())->method('generarProceso')
            ->willReturnCallback(function (string $id, $tipo, bool $force, ActividadAll $a): bool {
                $this->assertSame('3077', $id);
                $this->assertNull($tipo);
                $this->assertFalse($force);
                $this->assertSame(3077, $a->getId_activ());
                return true;
            });
        $this->procesos->method('consumirAvisosGenerarProceso')->willReturn(['sin proceso para el tipo']);
        $uc = $this->uc();
        $uc->crear_actividad($this->origen(12));
        $this->assertSame(['sin proceso para el tipo'], $uc->consumirAvisosProceso());
        $this->assertSame([], $uc->consumirAvisosProceso());
    }

    #[DataProvider('providerProcesos')]
    public function test_borrar_periodo_borra_proyectos_y_procesos_huerfanos(bool $procesos, int $sqls): void
    {
        if ($procesos) {
            $this->apps(['procesos']);
        }
        $this->actividades->expects($this->once())->method('deleteActividadesEnPeriodoEnProyecto')->with('2027-1-1', '2027-12-31');
        $sql = [];
        $this->actividades->expects($this->exactly($sqls))->method('execMaintenanceSql')
            ->willReturnCallback(function (string $s) use (&$sql): bool {
                $sql[] = $s;
                return true;
            });
        $this->actividades->method('getArrayActividadesEnPeriodoNoEnProyecto')->willReturn([]);
        $this->assertSame('', $this->uc()->borrar_actividades_periodo('2027-1-1', '2027-12-31'));
        if ($procesos) {
            $this->assertStringContainsString('a_actividad_proceso_sv', $sql[0]);
            $this->assertStringContainsString('a_actividad_proceso_sf', $sql[1]);
        }
    }

    public static function providerProcesos(): \Generator
    {
        yield 'procesos no instalado' => [false, 0];
        yield 'procesos instalado' => [true, 2];
    }

    public function test_borrar_periodo_avisa_de_errores_y_de_las_que_no_estan_en_proyecto(): void
    {
        $this->apps(['procesos']);
        $this->actividades->method('execMaintenanceSql')->willReturnOnConsecutiveCalls(false, false);
        $this->actividades->method('getArrayActividadesEnPeriodoNoEnProyecto')->willReturn([3001 => 'cv uno', 3002 => 'cv dos']);
        $txt = $this->uc()->borrar_actividades_periodo('2027-1-1', '2027-12-31');
        $this->assertSame(
            'error al borrar los procesos de la sv<br>error al borrar los procesos de la sf<br>'
            . 'actividades no eliminadas, porque su estado no es proyecto:<br>cv uno<br>cv dos<br>',
            $txt
        );
    }

    private function enCasa(string $nom, ?int $ubi, string $ini, string $fin, ?string $hIni = null, ?string $hFin = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setNom_activ($nom);
        $a->setId_ubi($ubi);
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setH_ini($hIni === null ? null : TimeLocal::fromString($hIni));
        $a->setH_fin($hFin === null ? null : TimeLocal::fromString($hFin));
        return $a;
    }

    /** @param list<ActividadAll> $lista */
    private function solapes(array $lista): string
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->assertSame(['dl_org eq dlb', 'f_ini between [2027-1-1,2027-12-31]', 'status lt 4'], DescribeCriteria::filtros($c));
            return $lista;
        });
        return $this->uc()->comprobar_solapes('2027-1-1', '2027-12-31');
    }

    public function test_solape_en_la_misma_casa(): void
    {
        $txt = $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-05'),
            $this->enCasa('B', 200, '2027-03-04', '2027-03-08'),
        ]);
        $this->assertSame('hay un solape entre:  A  y  B<br>', $txt);
    }

    public function test_cambio_de_turno_el_mismo_dia_no_es_solape_con_las_horas_por_defecto(): void
    {
        // Sin horas: la primera acaba a las 10:00 y la siguiente empieza a las 20:00.
        $this->assertSame('', $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-05'),
            $this->enCasa('B', 200, '2027-03-05', '2027-03-08'),
        ]));
    }

    public function test_cambio_de_turno_el_mismo_dia_con_horas_que_se_cruzan_es_solape(): void
    {
        $txt = $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-05', null, '18:00:00'),
            $this->enCasa('B', 200, '2027-03-05', '2027-03-08', '12:00:00'),
        ]);
        $this->assertSame('hay un solape entre:  A  y  B<br>', $txt);
    }

    public function test_casas_distintas_o_sin_determinar_no_solapan(): void
    {
        $this->assertSame('', $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-05'),
            $this->enCasa('B', 201, '2027-03-02', '2027-03-08'),
            $this->enCasa('C', 1, '2027-04-01', '2027-04-05'),
            $this->enCasa('D', 1, '2027-04-02', '2027-04-05'),
            $this->enCasa('E', null, '2027-05-01', '2027-05-05'),
            $this->enCasa('F', null, '2027-05-02', '2027-05-05'),
        ]));
    }

    public function test_solape_en_la_misma_casa_con_otra_casa_en_medio(): void
    {
        $txt = $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-05'),
            $this->enCasa('B', 201, '2027-03-02', '2027-03-03'),
            $this->enCasa('C', 200, '2027-03-04', '2027-03-08'),
        ]);
        $this->assertSame('hay un solape entre:  A  y  C<br>', $txt);
    }

    public function test_solape_con_una_actividad_larga_que_abarca_a_las_dos_siguientes(): void
    {
        $txt = $this->solapes([
            $this->enCasa('A', 200, '2027-03-01', '2027-03-20'),
            $this->enCasa('B', 200, '2027-03-02', '2027-03-03'),
            $this->enCasa('C', 200, '2027-03-10', '2027-03-12'),
        ]);
        $this->assertSame('hay un solape entre:  A  y  B<br>hay un solape entre:  A  y  C<br>', $txt);
    }

    // --- ActividadNuevoCursoEjecutar ---

    private function ejecutar(array $input): array
    {
        $uc = new ActividadNuevoCursoEjecutar($this->actividades, $this->repeticiones, $this->centros, $this->procesos);
        return $uc->ejecutar($input);
    }

    public function test_ejecutar_borra_el_anio_destino_copia_las_del_origen_y_cuenta_solo_las_creadas(): void
    {
        $this->actividades->expects($this->once())->method('deleteActividadesEnPeriodoEnProyecto')->with('2027-1-1', '2027-12-31');
        $this->actividades->method('getArrayActividadesEnPeriodoNoEnProyecto')->willReturn([]);
        $this->actividades->method('getNewId')->willReturn(77);
        $this->actividades->method('getNewIdActividad')->willReturn(3077);
        $this->actividades->expects($this->once())->method('Guardar')->willReturn(true);
        $consultas = [];
        $this->actividades->method('matching')->willReturnCallback(function ($c) use (&$consultas): array {
            $consultas[] = DescribeCriteria::filtros($c);
            if (count($consultas) === 1) {
                return [$this->origen(12), $this->origen(10), $this->origen(null, nom: 'sin rep')];
            }
            return [];
        });

        $out = $this->ejecutar(['year_ref' => '2026', 'year' => '2027', 'ver_lista' => '']);

        $this->assertSame(['dl_org in [dlb,dlbf]', 'status in [2,3]', 'f_ini between [2026-1-1,2026-12-31]'], $consultas[0]);
        $this->assertSame(['dl_org eq dlb', 'f_ini between [2027-1-1,2027-12-31]', 'status lt 4'], $consultas[1]);
        $this->assertSame(1, $out['copiadas']);
        $this->assertStringContainsString('<h3>1 actividades copiadas</h3>', $out['html']);
        $this->assertStringContainsString('<h3>errores al crear</h3>', $out['html']);
        $this->assertStringContainsString('sin rep', $out['html']);
        $this->assertStringNotContainsString('incidencias al borrar', $out['html']);
        $this->assertStringNotContainsString('tipo repetición', $out['html']);
    }

    public function test_ejecutar_con_lista_solapes_incidencias_y_avisos(): void
    {
        $this->apps(['procesos']);
        $this->actividades->method('execMaintenanceSql')->willReturn(true);
        $this->actividades->method('getArrayActividadesEnPeriodoNoEnProyecto')->willReturn([1 => 'cv actual']);
        $this->actividades->method('getNewId')->willReturn(77);
        $this->actividades->method('getNewIdActividad')->willReturn(3077);
        $this->actividades->method('Guardar')->willReturn(true);
        $this->procesos->method('consumirAvisosGenerarProceso')->willReturn(['<b>aviso</b>']);
        $n = 0;
        $this->actividades->method('matching')->willReturnCallback(function () use (&$n): array {
            $n++;
            if ($n === 1) {
                return [$this->origen(12)];
            }
            return [
                $this->enCasa('A', 200, '2027-03-01', '2027-03-05'),
                $this->enCasa('B', 200, '2027-03-04', '2027-03-08'),
            ];
        });

        $html = $this->ejecutar(['year_ref' => 2026, 'year' => 2027, 'ver_lista' => 't'])['html'];

        $this->assertStringStartsWith('tipo repetición => fechas_new :: nom_activ_new<br>2=> 7/9/2027-13/9/2027', $html);
        $this->assertStringContainsString('<h3>incidencias al borrar</h3>', $html);
        $this->assertStringContainsString('cv actual', $html);
        $this->assertStringContainsString('<h3>solapes</h3>hay un solape entre:  A  y  B', $html);
        $this->assertStringContainsString('<ul class="avisos"><li>&lt;b&gt;aviso&lt;/b&gt;</li></ul>', $html);
    }
}
