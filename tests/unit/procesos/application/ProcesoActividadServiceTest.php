<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\permisos\domain\XPermisos;
use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\entity\TareaProceso;
use src\procesos\domain\value_objects\FaseId;
use Tests\support\DescribeCriteria;

/**
 * Marcar y desmarcar fases del proceso de una actividad: dependencias previas
 * (se marcan solas si hay permiso), dependientes que impiden desmarcar,
 * referencias circulares, permiso de la oficina responsable y cambio del
 * status de la actividad (solo dre pasa a/desde actual; otra dl no se toca).
 *
 * Proceso de prueba (tipo 7): fase 1 (status 1) ← fase 2 (status 1) ← fase 5 (status 2, «aprobada»).
 */
final class ProcesoActividadServiceTest extends TestCase
{
    private const ACTIV = 3001;
    private const TIPO = 7;

    private ActividadProcesoTareaRepositoryInterface&MockObject $apt;
    private ActividadFaseRepositoryInterface&MockObject $fases;
    private TareaProcesoRepositoryInterface&MockObject $tareas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ?ActividadAll $actividad = null;
    /** @var array<string, bool> */
    private array $estado = [];
    /** @var array<string, array<string, string>> fase_tarea => [previa => mensaje] */
    private array $previas = [];
    /** @var array<int, int> id_fase => status */
    private array $statusFase = [1 => 1, 2 => 1, 5 => 2];
    /** @var array<int, ?int> id_fase => id_of_responsable */
    private array $responsable = [];
    /** @var list<string> fase_tarea marcadas (true) / desmarcadas (false) con DBMarcar */
    private array $marcadas = [];
    private mixed $sesion = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION ?? [];
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        unset($_SESSION['oPerm']);
        $this->apt = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $this->fases = $this->createMock(ActividadFaseRepositoryInterface::class);
        $this->tareas = $this->createMock(TareaProcesoRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);

        $this->actividades->method('findById')->willReturnCallback(fn (): ?ActividadAll => $this->actividad);
        $this->tareas->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $idFase = (int) substr($f[1], strlen('id_fase eq '));
            if (!isset($this->statusFase[$idFase])) {
                return [];
            }
            $t = new TareaProceso();
            $t->setId_fase($idFase);
            $t->setId_tarea(0);
            $t->setStatus($this->statusFase[$idFase]);
            $t->setId_of_responsable($this->responsable[$idFase] ?? null);
            return [$t];
        });
        $this->tareas->method('arbolPrevio')->willReturnCallback(fn (): array => $this->previas);
        $this->tareas->method('getArrayFasesDependientes')->willReturnCallback(function (): array {
            $dep = [];
            foreach ($this->previas as $fase => $aPrevias) {
                $dep[$fase] = [$aPrevias];
            }
            return $dep;
        });
        $this->tareas->method('getStatusProceso')->willReturnCallback(function (int $tipo, array $estado): int {
            $max = 0;
            foreach ($estado as $ft => $on) {
                if ($on) {
                    $max = max($max, $this->statusFase[(int) $ft]);
                }
            }
            return $max;
        });
        $this->apt->method('getListaFaseEstado')->willReturnCallback(fn (): array => $this->estado);
        $this->apt->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $apt = new ActividadProcesoTarea();
            $apt->setId_activ(self::ACTIV);
            $apt->setId_fase((int) substr($f[1] ?? 'id_fase eq 0', strlen('id_fase eq ')));
            $apt->setId_tarea(0);
            return [$apt];
        });
        $this->apt->method('DBMarcar')->willReturnCallback(function (ActividadProcesoTarea $a): bool {
            $this->marcadas[] = $a->getId_fase() . '#0=' . ($a->isCompletado() ? 'on' : 'off');
            return true;
        });
        $this->fases->method('findById')->willReturnCallback(function (int $id): ActividadFase {
            $f = new ActividadFase();
            $f->setDesc_fase("fase $id");
            return $f;
        });
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesion;
    }

    private function svc(): ProcesoActividadService
    {
        return new ProcesoActividadService($this->apt, $this->fases, $this->tareas, $this->actividades);
    }

    private function actividad(int $status, string $dlOrg = 'dlb', string $idTabla = 'dl'): void
    {
        $a = new ActividadAll();
        $a->setId_activ(self::ACTIV);
        $a->setStatus($status);
        $a->setDl_org($dlOrg);
        $a->setId_tabla($idTabla);
        $this->actividad = $a;
    }

    private function tarea(int $idFase, bool $completado): ActividadProcesoTarea
    {
        $t = new ActividadProcesoTarea();
        $t->setId_activ(self::ACTIV);
        $t->setId_tipo_proceso(self::TIPO);
        $t->setId_fase($idFase);
        $t->setId_tarea(0);
        $t->setCompletado($completado);
        return $t;
    }

    private function permisos(string ...$oficinas): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $of): bool => in_array($of, $oficinas, true));
        $_SESSION['oPerm'] = $perm;
    }

    public function test_marcar_una_fase_sin_previas_la_guarda_sin_tocar_el_status(): void
    {
        $this->actividad(1);
        $this->estado = ['1#0' => false, '2#0' => false, '5#0' => false];
        $this->apt->expects($this->once())->method('Guardar')->willReturn(true);
        $this->actividades->expects($this->never())->method('Guardar');
        $svc = $this->svc();
        $this->assertTrue($svc->guardar($this->tarea(1, true)));
        $this->assertSame(['1#0=on'], $this->marcadas);
        $this->assertSame('', $svc->getErrorTxt());
    }

    public function test_marcar_con_previas_sin_completar_las_marca_si_hay_permiso(): void
    {
        $this->actividad(1);
        $this->previas = ['2#0' => ['1#0' => ''], '5#0' => ['2#0' => '']];
        $this->estado = ['1#0' => false, '2#0' => false, '5#0' => false];
        $this->apt->method('Guardar')->willReturn(true);
        $this->assertTrue($this->svc()->guardar($this->tarea(2, true)));
        $this->assertSame(['1#0=on', '2#0=on'], $this->marcadas);
    }

    public function test_marcar_con_una_previa_de_otra_oficina_no_se_puede(): void
    {
        $this->actividad(1);
        $this->previas = ['2#0' => ['1#0' => '']];
        $this->estado = ['1#0' => false, '2#0' => false];
        $this->responsable = [1 => 16]; // est
        $this->permisos('agd');
        $this->apt->expects($this->never())->method('Guardar');
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(2, true)));
        $this->assertSame('No tienen completada la fase: fase 1', $svc->getErrorTxt());
        $this->assertSame([], $this->marcadas);
    }

    public function test_previa_con_mensaje_propio(): void
    {
        $this->actividad(1);
        $this->previas = ['2#0' => ['1#0' => 'falta la 1']];
        $this->estado = ['1#0' => false, '2#0' => false];
        $this->responsable = [1 => 16];
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(2, true)));
        $this->assertSame('falta la 1', $svc->getErrorTxt());
    }

    public function test_sin_permiso_de_la_oficina_responsable_no_se_marca(): void
    {
        $this->actividad(1);
        $this->estado = ['1#0' => false];
        $this->responsable = [1 => 16];
        $this->permisos('agd');
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(1, true)));
        $this->assertSame('No tiene permiso para marcar o desmarcar esta fase', $svc->getErrorTxt());

        $this->permisos('est');
        $this->apt->method('Guardar')->willReturn(true);
        $this->assertTrue($svc->guardar($this->tarea(1, true)));
    }

    public function test_referencias_circulares(): void
    {
        $this->actividad(1);
        $this->previas = ['1#0' => ['2#0' => ''], '2#0' => ['1#0' => '']];
        $this->estado = ['1#0' => false, '2#0' => false];
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(1, true)));
        $this->assertSame('Hay un error en el diseño del proceso: referencias circulares.', $svc->getErrorTxt());
    }

    public function test_desmarcar_con_dependientes_marcadas_avisa_de_cada_una(): void
    {
        $this->actividad(1);
        $this->previas = ['2#0' => ['1#0' => ''], '5#0' => ['1#0' => '']];
        $this->statusFase[5] = 1;
        $this->estado = ['1#0' => true, '2#0' => true, '5#0' => true];
        $this->apt->expects($this->never())->method('Guardar');
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(1, false)));
        $this->assertSame(
            'La fase: fase 2 está marcada, y depende de esta.La fase: fase 5 está marcada, y depende de esta.',
            $svc->getErrorTxt(),
        );
    }

    public function test_desmarcar_sin_dependientes_recalcula_el_status(): void
    {
        $this->actividad(StatusId::ACTUAL, 'dlb');
        $this->permisos('des');
        $this->estado = ['1#0' => true, '2#0' => false, '5#0' => true];
        $this->apt->method('Guardar')->willReturn(true);
        $this->actividades->expects($this->once())->method('Guardar')->willReturnCallback(function (ActividadAll $a): bool {
            $this->assertSame(1, $a->getStatus(), 'vuelve a proyecto');
            return true;
        });
        $this->assertTrue($this->svc()->guardar($this->tarea(5, false)));
        $this->assertSame(['5#0=off'], $this->marcadas);
    }

    public function test_pasar_a_actual_solo_lo_puede_hacer_dre(): void
    {
        $this->actividad(1, 'dlb');
        $this->estado = ['1#0' => true, '2#0' => true, '5#0' => false];
        $this->permisos('est');
        $this->actividades->expects($this->never())->method('Guardar');
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(5, true)));
        $this->assertStringContainsString('debe hacerlo dre', $svc->getErrorTxt());
    }

    public function test_dre_pasa_a_actual_y_marca_la_fase_aprobada_en_sf(): void
    {
        $this->actividad(1, 'dlb');
        $this->estado = ['1#0' => true, '2#0' => true, '5#0' => false];
        $this->permisos('des');
        $tablas = [];
        $this->apt->method('setNomTabla')->willReturnCallback(function (string $t) use (&$tablas): void {
            $tablas[] = $t;
        });
        $guardadas = [];
        $this->apt->method('Guardar')->willReturnCallback(function (ActividadProcesoTarea $t) use (&$guardadas): bool {
            $guardadas[] = $t->getId_fase() . '=' . ($t->isCompletado() ? 'on' : 'off');
            return true;
        });
        $this->actividades->expects($this->once())->method('Guardar')->willReturnCallback(function (ActividadAll $a): bool {
            $this->assertSame(StatusId::ACTUAL, $a->getStatus());
            return true;
        });
        $this->assertTrue($this->svc()->guardar($this->tarea(5, true)));
        $this->assertSame(['a_actividad_proceso_sf', 'a_actividad_proceso_sv'], $tablas, 'mira el proceso de sf y repone la tabla');
        $this->assertSame([FaseId::FASE_APROBADA . '=on', '5=on'], $guardadas);
    }

    public function test_actividad_de_otra_dl_marca_la_fase_pero_no_cambia_el_status(): void
    {
        $this->statusFase = [1 => 1, 2 => 3, 5 => 2];
        $this->actividad(1, 'dlp', 'dl');
        $this->estado = ['1#0' => true, '2#0' => false];
        $this->apt->expects($this->once())->method('Guardar')->willReturn(true);
        $this->actividades->expects($this->never())->method('Guardar');
        $svc = $this->svc();
        $this->assertTrue($svc->guardar($this->tarea(2, true)));
        $this->assertSame('no se puede modificar el status de una actividad de otra dl (dlp)', $svc->getErrorTxt());
    }

    public function test_actividad_de_otra_region_cambia_el_status(): void
    {
        $this->statusFase = [1 => 1, 2 => 3];
        $this->actividad(1, 'crX', 'ex');
        $this->estado = ['1#0' => true, '2#0' => false];
        $this->apt->method('Guardar')->willReturn(true);
        $this->actividades->expects($this->once())->method('Guardar');
        $this->assertTrue($this->svc()->guardar($this->tarea(2, true)));
    }

    public function test_errores_de_datos(): void
    {
        $svc = $this->svc();
        $this->assertFalse($svc->guardar($this->tarea(1, true)));
        $this->assertSame('Actividad 3001 no encontrada', $svc->getErrorTxt());

        $this->actividad(1);
        $this->assertFalse($svc->guardar($this->tarea(9, true)));
        $this->assertStringContainsString('fase: 9', $svc->getErrorTxt());

        $t = $this->tarea(1, true);
        $t->setId_fase(null);
        $this->assertFalse($svc->guardar($t));
        $this->assertSame('Fase o tarea no definida en el proceso', $svc->getErrorTxt());
    }

    public function test_el_servicio_se_puede_reusar_en_un_lote(): void
    {
        $this->actividad(1);
        $this->estado = ['1#0' => false];
        $this->apt->method('Guardar')->willReturn(true);
        $svc = $this->svc();
        $this->assertTrue($svc->guardar($this->tarea(1, true)));
        $this->assertTrue($svc->guardar($this->tarea(1, true)), 'sin falsas referencias circulares');
    }
}
