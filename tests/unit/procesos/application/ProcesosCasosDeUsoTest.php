<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\TipoDeActividad;
use src\permisos\domain\XPermisos;
use src\procesos\application\ActividadProcesoGet;
use src\procesos\application\ActividadProcesoUpdate;
use src\procesos\application\ActividadQueFasesCuadro;
use src\procesos\application\FasesActivCambioGet;
use src\procesos\application\FasesActivCambioUpdate;
use src\procesos\application\ProcesoActividadService;
use src\procesos\application\ProcesosVerData;
use src\procesos\application\TipoActivProcesoLista;
use src\procesos\application\UsuarioPermActivFases;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\entity\ActividadTarea;
use src\procesos\domain\entity\ProcesoTipo;
use src\procesos\domain\entity\TareaProceso;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Casos de uso de procesos: tareas de una actividad, cambio de fase en bloque, desplegables y pantalla de edición. */
final class ProcesosCasosDeUsoTest extends myTest
{
    private ActividadProcesoTareaRepositoryInterface&MockObject $apt;
    private ActividadFaseRepositoryInterface&MockObject $fases;
    private ActividadTareaRepositoryInterface&MockObject $tareas;
    private TareaProcesoRepositoryInterface&MockObject $tareasProceso;
    private ProcesoActividadService&MockObject $servicio;
    private TipoDeActividadRepositoryInterface&MockObject $tipos;
    private mixed $permAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        unset($_SESSION['oPerm']);
        $this->apt = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $this->fases = $this->createMock(ActividadFaseRepositoryInterface::class);
        $this->tareas = $this->createMock(ActividadTareaRepositoryInterface::class);
        $this->tareasProceso = $this->createMock(TareaProcesoRepositoryInterface::class);
        $this->servicio = $this->createMock(ProcesoActividadService::class);
        $this->tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        if ($this->permAnterior === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->permAnterior;
        }
        parent::tearDown();
    }

    private function permiso(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function tarea(int $item, int $tipoProceso, ?int $fase, ?int $tarea, bool $completado = false, ?string $observ = null): ActividadProcesoTarea&MockObject
    {
        $t = $this->createMock(ActividadProcesoTarea::class);
        $t->method('getId_item')->willReturn($item);
        $t->method('getId_tipo_proceso')->willReturn($tipoProceso);
        $t->method('getId_fase')->willReturn($fase);
        $t->method('getId_tarea')->willReturn($tarea);
        $t->method('isCompletado')->willReturn($completado);
        $t->method('getObserv')->willReturn($observ);

        return $t;
    }

    private function fase(?string $desc): ActividadFase&MockObject
    {
        $f = $this->createMock(ActividadFase::class);
        $f->method('getDesc_fase')->willReturn($desc);

        return $f;
    }

    private function tareaDef(?string $desc): ActividadTarea&MockObject
    {
        $t = $this->createMock(ActividadTarea::class);
        $t->method('getDesc_tarea')->willReturn($desc);

        return $t;
    }

    private function tareaProceso(string $responsable): TareaProceso&MockObject
    {
        $t = $this->createMock(TareaProceso::class);
        $t->method('getOf_responsable_txt')->willReturn($responsable);

        return $t;
    }

    // ---- ActividadProcesoGet ----------------------------------------------------------------------

    private function get(): ActividadProcesoGet
    {
        return new ActividadProcesoGet($this->apt, $this->fases, $this->tareas, $this->tareasProceso);
    }

    public function test_sin_tareas_la_lista_esta_vacia(): void
    {
        $this->apt->method('matching')->willReturn([]);

        $this->assertSame(['error' => '', 'a_rows' => []], $this->get()->execute(['id_activ' => '3001']));
    }

    public function test_cada_tarea_lleva_fase_tarea_y_firma_y_se_saltan_las_incompletas(): void
    {
        $criterio = null;
        $this->apt->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = DescribeCriteria::filtros($c);

            return [
                $this->tarea(1, 5, 10, 20, true, 'ok'),
                $this->tarea(2, 5, null, 20),
                $this->tarea(3, 5, 11, 20),
                $this->tarea(4, 5, 12, 21),
                $this->tarea(5, 5, 13, 22),
            ];
        });
        $this->fases->method('findById')->willReturnMap([[10, $this->fase('Fase A')], [11, null], [12, $this->fase('')], [13, $this->fase('Fase D')]]);
        $this->tareas->method('findById')->willReturnMap([[20, $this->tareaDef('Tarea X')], [22, null]]);
        $this->tareasProceso->method('matching')->willReturn([$this->tareaProceso('')]);

        $r = $this->get()->execute(['id_activ' => '3001']);

        $this->assertSame(['id_activ eq 3001'], $criterio);
        $this->assertSame('', $r['error']);
        $this->assertCount(1, $r['a_rows']);
        $fila = $r['a_rows'][0];
        $this->assertSame([1, 'Fase A', 'Tarea X', true, 'ok', true], [$fila['id_item'], $fila['fase'], $fila['tarea'], $fila['completado'], $fila['observ'], $fila['puede_editar']]);
        $this->assertNotSame('', $fila['ctx_update']);
    }

    public function test_solo_puede_editar_quien_tiene_el_permiso_de_la_oficina_responsable(): void
    {
        $this->apt->method('matching')->willReturn([$this->tarea(1, 5, 10, 20)]);
        $this->fases->method('findById')->willReturn($this->fase('Fase A'));
        $this->tareas->method('findById')->willReturn($this->tareaDef('Tarea X'));
        $this->tareasProceso->method('matching')->willReturn([$this->tareaProceso('des')]);

        $sin = $this->get()->execute(['id_activ' => 1])['a_rows'][0]['puede_editar'];
        $this->permiso(['des']);
        $con = $this->get()->execute(['id_activ' => 1])['a_rows'][0]['puede_editar'];
        $this->permiso(['sm']);
        $otra = $this->get()->execute(['id_activ' => 1])['a_rows'][0]['puede_editar'];

        $this->assertSame([false, true, false], [$sin, $con, $otra]);
    }

    public function test_una_tarea_sin_definicion_en_el_tipo_de_proceso_corta_con_error(): void
    {
        $this->apt->method('matching')->willReturn([$this->tarea(1, 5, 10, 20)]);
        $this->fases->method('findById')->willReturn($this->fase('Fase A'));
        $this->tareas->method('findById')->willReturn($this->tareaDef('Tarea X'));
        $this->tareasProceso->method('matching')->willReturn([]);

        $r = $this->get()->execute(['id_activ' => 1]);

        $this->assertStringContainsString('5', $r['error']);
        $this->assertSame([], $r['a_rows']);
    }

    // ---- ActividadProcesoUpdate ----------------------------------------------------------------------

    private function update(): ActividadProcesoUpdate
    {
        return new ActividadProcesoUpdate($this->servicio, $this->apt);
    }

    public function test_actualizar_una_tarea_inexistente_avisa(): void
    {
        $this->apt->method('findById')->willReturn(null);
        $this->servicio->expects($this->never())->method('guardar');

        $this->assertNotSame('', $this->update()->execute(['id_item' => 9]));
    }

    public function test_actualizar_marca_completado_y_observaciones(): void
    {
        $t = $this->createMock(ActividadProcesoTarea::class);
        $this->apt->method('findById')->with(9)->willReturn($t);
        $t->expects($this->once())->method('setCompletado')->with(true);
        $t->expects($this->once())->method('setObserv')->with('hecho');
        $this->servicio->method('guardar')->willReturn(true);

        $this->assertSame('', $this->update()->execute(['id_item' => '9', 'completado' => 'on', 'observ' => 'hecho']));
    }

    /** Antes la excepción del VO salía como texto suelto (ni JSON ni aviso en la pantalla). */
    public function test_observacion_no_valida_devuelve_el_error_sin_guardar(): void
    {
        $this->apt->method('findById')->willReturn(new ActividadProcesoTarea());
        $this->servicio->expects($this->never())->method('guardar');

        $r = $this->update()->execute(['id_item' => '9', 'completado' => 'true', 'observ' => "con\x07campana"]);

        $this->assertStringStartsWith('hay un error, no se ha guardado', $r);
    }

    public function test_el_error_del_servicio_tiene_prioridad_sobre_el_del_repositorio(): void
    {
        $this->apt->method('findById')->willReturn($this->createMock(ActividadProcesoTarea::class));
        $this->servicio->method('guardar')->willReturn(false);
        $this->servicio->method('getErrorTxt')->willReturnOnConsecutiveCalls('fase previa sin completar', '');
        $this->apt->method('getErrorTxt')->willReturn('sql');
        $uc = $this->update();

        $this->assertSame('fase previa sin completar', $uc->execute(['id_item' => 9]));
        $this->assertStringContainsString('sql', $uc->execute(['id_item' => 9]));
    }

    // ---- FasesActivCambioUpdate ----------------------------------------------------------------------

    private function cambio(): FasesActivCambioUpdate
    {
        return new FasesActivCambioUpdate($this->actividades(), $this->apt, $this->tareasProceso, $this->servicio);
    }

    private ?ActividadAllRepositoryInterface $actividadesRepo = null;

    private function actividades(): ActividadAllRepositoryInterface
    {
        return $this->actividadesRepo ??= $this->createMock(ActividadAllRepositoryInterface::class);
    }

    public function test_si_la_actividad_no_tiene_esa_fase_se_avisa_de_regenerar_el_proceso(): void
    {
        $this->apt->method('matching')->willReturn([]);
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('ca invierno');
        $this->actividades()->method('findById')->willReturn($a);

        $r = $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3001#x'], 'accion' => 'marcar']);

        $this->assertStringContainsString('ca invierno', $r);
        $this->assertStringContainsString('3001', $r);
        $this->assertStringContainsString('<br>', $r);
    }

    public function test_marcar_y_desmarcar_cambian_completado_si_se_tiene_permiso(): void
    {
        $t1 = $this->createMock(ActividadProcesoTarea::class);
        $t2 = $this->createMock(ActividadProcesoTarea::class);
        foreach ([$t1, $t2] as $t) {
            $t->method('getId_tipo_proceso')->willReturn(5);
            $t->method('getId_fase')->willReturn(30);
            $t->method('getId_tarea')->willReturn(1);
        }
        $this->apt->method('matching')->willReturnOnConsecutiveCalls([$t1], [$t2]);
        $this->tareasProceso->method('matching')->willReturn([$this->tareaProceso('')]);
        $this->servicio->method('guardar')->willReturn(true);
        $t1->expects($this->once())->method('setCompletado')->with(true);
        $t2->expects($this->once())->method('setCompletado')->with(false);

        $this->assertSame('', $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3001'], 'accion' => 'marcar']));
        $this->assertSame('', $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3002'], 'accion' => 'desmarcar']));
    }

    private function conIds(): ActividadProcesoTarea&MockObject
    {
        $t = $this->createMock(ActividadProcesoTarea::class);
        $t->method('getId_tipo_proceso')->willReturn(5);
        $t->method('getId_fase')->willReturn(30);
        $t->method('getId_tarea')->willReturn(1);

        return $t;
    }

    public function test_sin_permiso_de_la_oficina_responsable_no_se_guarda(): void
    {
        $t = $this->conIds();
        $this->apt->method('matching')->willReturn([$t]);
        $this->tareasProceso->method('matching')->willReturn([$this->tareaProceso('des')]);
        $t->expects($this->never())->method('setCompletado');
        $this->servicio->expects($this->never())->method('guardar');

        $this->assertNotSame('', $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3001'], 'accion' => 'marcar']));
    }

    public function test_un_error_de_guardado_se_acumula_y_se_sigue_con_las_demas(): void
    {
        $t = $this->conIds();
        $this->apt->method('matching')->willReturn([$t]);
        $this->tareasProceso->method('matching')->willReturn([$this->tareaProceso('')]);
        $this->servicio->expects($this->exactly(2))->method('guardar')->willReturn(false);
        $this->servicio->method('getErrorTxt')->willReturnOnConsecutiveCalls('E1', '');
        $this->apt->method('getErrorTxt')->willReturn('sql');

        $r = $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3001', '3002'], 'accion' => 'marcar']);

        $this->assertStringStartsWith('E1<br>', $r);
        $this->assertStringContainsString('sql', $r);
    }

    public function test_una_fase_sin_definicion_en_el_tipo_de_proceso_corta_la_operacion(): void
    {
        $t = $this->createMock(ActividadProcesoTarea::class);
        $t->method('getId_tipo_proceso')->willReturn(5);
        $t->method('getId_fase')->willReturn(30);
        $t->method('getId_tarea')->willReturn(1);
        $this->apt->method('matching')->willReturn([$t]);
        $this->tareasProceso->method('matching')->willReturn([]);
        $this->servicio->expects($this->never())->method('guardar');

        $r = $this->cambio()->execute(['id_fase_nueva' => '30', 'sel' => ['3001', '3002'], 'accion' => 'marcar']);

        $this->assertStringContainsString('30', $r);
    }

    // ---- desplegables de fases ----------------------------------------------------------------------

    public function test_cuadro_de_fases_marca_las_seleccionadas(): void
    {
        $this->tipos->expects($this->once())->method('getTiposDeProcesos')->with('131001', false)->willReturn([5]);
        $this->fases->method('getArrayFasesProcesos')->with([5])->willReturn(['Fase A' => 10, 'Fase B' => '11']);

        $r = (new ActividadQueFasesCuadro($this->tipos, $this->fases))->ejecutar('131001', false, [11]);

        $this->assertSame(['a_fases' => [
            ['id' => 10, 'nom' => 'Fase A', 'checked' => false],
            ['id' => 11, 'nom' => 'Fase B', 'checked' => true],
        ]], $r);
    }

    /**
     * @dataProvider dlPropiaProvider
     */
    public function test_el_desplegable_de_cambio_de_fase_pasa_dl_propia_y_orden(string $dlPropia, bool $esperado): void
    {
        $this->tipos->expects($this->once())->method('getTiposDeProcesos')->with('131001', $esperado)->willReturn([5]);
        $this->fases->method('getArrayActividadFases')->with([5], true)->willReturn([30 => 'Fase Z', 5 => 'Fase A']);

        $r = (new FasesActivCambioGet($this->tipos, $this->fases))->execute(['id_tipo_activ' => '131001', 'dl_propia' => $dlPropia, 'id_fase_sel' => '30']);

        $this->assertSame([['30', 'Fase Z'], ['5', 'Fase A']], $r['opciones']);
        $this->assertSame(['id_fase_nueva', '30', true, 'fnjs_lista()'], [$r['id'], $r['selected'], $r['blanco'], $r['action']]);
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function dlPropiaProvider(): iterable
    {
        yield 'propia' => ['t', true];
        yield 'ajena' => ['f', false];
        yield 'vacía' => ['', false];
    }

    public function test_las_fases_del_permiso_por_usuario_se_devuelven_como_mapa(): void
    {
        $this->tipos->method('getTiposDeProcesos')->with('131001', true)->willReturn([5]);
        $this->fases->method('getArrayActividadFases')->with([5])->willReturn([5 => 'Fase A']);

        $r = (new UsuarioPermActivFases($this->tipos, $this->fases))->execute(['id_tipo_activ' => '131001', 'dl_propia' => 'true']);

        $this->assertSame(['opciones' => [5 => 'Fase A']], $r);
    }

    // ---- ProcesosVerData ----------------------------------------------------------------------

    private function ver(): ProcesosVerData
    {
        return new ProcesosVerData($this->fases, $this->tareas, $this->tareasProceso);
    }

    public function test_un_alta_ofrece_una_fila_previa_vacia_y_las_listas_de_apoyo(): void
    {
        $this->fases->method('getArrayActividadFases')->willReturn([1 => 'F1']);

        $r = $this->ver()->execute('nuevo', 0, 5);

        $this->assertSame([1 => 'F1'], $r['a_fases']);
        $this->assertCount(1, $r['a_fases_previas']);
        $this->assertSame('', $r['a_fases_previas'][0]['id_fase_previa']);
        $this->assertNotSame([], $r['a_oficinas']);
        $this->assertNotSame([], $r['a_status']);
        $this->assertNotSame('', $r['ctx_update']);
    }

    public function test_editar_una_tarea_inexistente_devuelve_el_formulario_base_sin_firma(): void
    {
        $this->tareasProceso->method('findById')->willReturn(null);

        $r = $this->ver()->execute('editar', 9);

        $this->assertSame('editar', $r['mod']);
        $this->assertSame('', $r['id_fase']);
        $this->assertArrayNotHasKey('ctx_update', $r);
    }

    public function test_editar_carga_la_tarea_sus_tareas_y_las_fases_previas_validas(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $t->method('getStatus')->willReturn(2);
        $t->method('getId_of_responsable')->willReturn(7);
        $t->method('getId_fase')->willReturn(30);
        $t->method('getId_tarea')->willReturn(1);
        $t->method('getJsonFasesPreviasAsList')->willReturn([
            ['id_fase' => '10', 'id_tarea' => '2', 'mensaje' => 'antes'],
            ['id_fase' => '', 'id_tarea' => '3'],
            ['id_fase' => 'x'],
        ]);
        $this->tareasProceso->method('findById')->with(9)->willReturn($t);
        $this->tareas->method('getArrayActividadTareas')->willReturnCallback(fn (int $f): array => [$f => "tareas de $f"]);

        $r = $this->ver()->execute('editar', 9, 5);

        $this->assertSame([2, 7, 30, 1], [$r['status'], $r['id_of_responsable'], $r['id_fase'], $r['id_tarea']]);
        $this->assertSame([30 => 'tareas de 30'], $r['a_tareas']);
        $this->assertCount(1, $r['a_fases_previas']);
        $this->assertSame(['10', '2', 'antes', [10 => 'tareas de 10']], array_values($r['a_fases_previas'][0]));
    }

    public function test_si_ninguna_fase_previa_es_valida_queda_una_fila_vacia(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $t->method('getId_fase')->willReturn(30);
        $t->method('getJsonFasesPreviasAsList')->willReturn([['id_fase' => 'x']]);
        $this->tareasProceso->method('findById')->willReturn($t);
        $this->tareas->method('getArrayActividadTareas')->willReturn([]);

        $this->assertSame([['id_fase_previa' => '', 'id_tarea_previa' => '', 'mensaje_requisito' => '', 'a_tareas_previa' => []]], $this->ver()->execute('editar', 9)['a_fases_previas']);
    }

    // ---- TipoActivProcesoLista ----------------------------------------------------------------------

    public function test_la_lista_de_tipos_muestra_el_proceso_propio_y_el_no_dl_o_guiones(): void
    {
        $tipo = $this->createMock(TipoDeActividad::class);
        $tipo->method('getId_tipo_activ')->willReturn(131001);
        $tipo->method('getId_tipo_proceso')->willReturn(5);
        $tipo->method('getId_tipo_proceso_ex')->willReturn(null);
        $this->tipos->method('matching')->willReturn([$tipo]);
        $pt = $this->createMock(ProcesoTipo::class);
        $pt->method('getId_tipo_proceso')->willReturn(5);
        $pt->method('getNom_proceso')->willReturn('Proceso sv');
        $procesos = $this->createMock(ProcesoTipoRepositoryInterface::class);
        $procesos->method('matching')->willReturn([$pt]);

        $r = (new TipoActivProcesoLista($this->tipos, $procesos))->execute();

        $this->assertCount(4, $r['a_cabeceras']);
        $this->assertSame([131001, 5, 'Proceso sv', 0, '----'], [
            $r['a_tipos'][0]['id_tipo_activ'], $r['a_tipos'][0]['id_tipo_proceso'], $r['a_tipos'][0]['nom_proceso_propio'],
            $r['a_tipos'][0]['id_tipo_proceso_ex'], $r['a_tipos'][0]['nom_proceso_no_propio'],
        ]);
    }
}
