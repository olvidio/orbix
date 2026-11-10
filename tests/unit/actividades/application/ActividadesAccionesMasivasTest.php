<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ActividadCambiarTipo;
use src\actividades\application\ActividadDuplicar;
use src\actividades\application\ActividadEliminar;
use src\actividades\application\ActividadImportar;
use src\actividades\application\ActividadPublicar;
use src\actividades\application\BorrarActividad;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\StatusId;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\procesos\domain\PermAccionBits;
use Tests\myTest;

/** Acciones sobre actividades seleccionadas: publicar, importar, duplicar, eliminar y cambiar de tipo. */
final class ActividadesAccionesMasivasTest extends myTest
{
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPerm', 'oPermActividades', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
            unset($_SESSION[$k]);
        }
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function procesosInstalado(bool $si): void
    {
        $_SESSION['config'] = $si ? ['a_apps' => ['procesos' => 3], 'app_installed' => [3]] : [];
    }

    // ---- publicar ----------------------------------------------------------------------

    public function test_publicar_marca_las_encontradas_y_acumula_errores_de_guardado(): void
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $a1 = $this->createMock(ActividadAll::class);
        $a1->expects($this->once())->method('setPublicado')->with(true);
        $a2 = $this->createMock(ActividadAll::class);
        $repo->method('findById')->willReturnMap([[3001, $a1], [3002, null], [3003, $a2]]);
        $repo->method('Guardar')->willReturnOnConsecutiveCalls(true, false);
        $repo->method('getErrorTxt')->willReturn('sql');
        $uc = new ActividadPublicar($repo);

        $this->assertSame('', $uc->execute([]));
        $r = $uc->execute(['sel' => ['3001#x', '3002', '3003']]);

        $this->assertStringContainsString('sql', $r);
        $this->assertSame(1, substr_count($r, 'sql'), 'solo falla una');
    }

    // ---- importar ----------------------------------------------------------------------

    public function test_importar_sin_seleccion_no_hace_nada(): void
    {
        $importadas = $this->createMock(ImportadaRepositoryInterface::class);
        $importadas->expects($this->never())->method('Guardar');

        $r = (new ActividadImportar($importadas, $this->createMock(ActividadProcesoTareaRepositoryInterface::class)))->execute([]);

        $this->assertSame(['error_txt' => '', 'avisos' => []], $r);
    }

    public function test_importar_guarda_cada_actividad_y_regenera_su_proceso_con_los_avisos(): void
    {
        $this->procesosInstalado(true);
        $importadas = $this->createMock(ImportadaRepositoryInterface::class);
        $ids = [];
        $importadas->method('Guardar')->willReturnCallback(function (Importada $i) use (&$ids): bool {
            $ids[] = $i->getId_activ();

            return true;
        });
        $procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $procesos->expects($this->exactly(2))->method('generarProceso')->with($this->anything(), 1, true);
        $procesos->method('consumirAvisosGenerarProceso')->willReturnOnConsecutiveCalls(['aviso 1'], []);

        $r = (new ActividadImportar($importadas, $procesos))->execute(['sel' => ['3001#a', '3002']]);

        $this->assertSame([3001, 3002], $ids);
        $this->assertSame(['error_txt' => '', 'avisos' => ['aviso 1']], $r);
    }

    public function test_importar_sin_la_app_de_procesos_no_genera_proceso(): void
    {
        $this->procesosInstalado(false);
        $importadas = $this->createMock(ImportadaRepositoryInterface::class);
        $importadas->method('Guardar')->willReturn(false);
        $importadas->method('getErrorTxt')->willReturn('dup');
        $procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $procesos->expects($this->never())->method('generarProceso');

        $r = (new ActividadImportar($importadas, $procesos))->execute(['sel' => ['3001']]);

        $this->assertStringContainsString('dup', $r['error_txt']);
    }

    // ---- duplicar ----------------------------------------------------------------------

    private function duplicar(?ActividadAll $actividad, ActividadDlRepositoryInterface &$repo = null): ActividadDuplicar
    {
        $repo = $this->createMock(ActividadDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($actividad);

        return new ActividadDuplicar($repo);
    }

    public function test_duplicar_exige_seleccion_y_que_exista(): void
    {
        $this->assertNotSame('', $this->duplicar(null)->execute([]));
        $this->assertNotSame('', $this->duplicar(null)->execute(['sel' => ['3001']]));
    }

    public function test_solo_se_duplican_actividades_de_la_propia_dl(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getDl_org')->willReturn('dlp');
        $uc = $this->duplicar($a, $repo);
        $repo->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $uc->execute(['sel' => ['3001']]));
    }

    public function test_duplicar_crea_una_copia_en_proyecto_con_nombre_dup_y_nuevo_id(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getDl_org')->willReturn('dlb');
        $a->method('getNom_activ')->willReturn('ca invierno');
        $a->expects($this->once())->method('setId_activ')->with(3999);
        $a->expects($this->once())->method('setNom_activ')->with($this->stringEndsWith('ca invierno'));
        $a->expects($this->once())->method('setStatus')->with(StatusId::PROYECTO);
        $uc = $this->duplicar($a, $repo);
        $repo->method('getNewId')->willReturn(7);
        $repo->method('getNewIdActividad')->with(7)->willReturn(3999);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', $uc->execute(['sel' => ['3001#x']]));
    }

    public function test_con_permiso_des_se_puede_duplicar_la_de_sf_de_mi_dl(): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => $o === MenuDlPermissionBits::DES);
        $_SESSION['oPerm'] = $perm;
        $a = $this->createMock(ActividadAll::class);
        $a->method('getDl_org')->willReturn('dlbf');
        $a->method('getNom_activ')->willReturn('x');
        $uc = $this->duplicar($a, $repo);
        $repo->method('Guardar')->willReturn(true);

        $this->assertSame('', $uc->execute(['sel' => ['3001']]));
    }

    public function test_un_fallo_de_guardado_al_duplicar_se_devuelve(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getDl_org')->willReturn('dlb');
        $a->method('getNom_activ')->willReturn('x');
        $uc = $this->duplicar($a, $repo);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('sql');

        $this->assertStringContainsString('sql', $uc->execute(['sel' => ['3001']]));
    }

    // ---- eliminar ----------------------------------------------------------------------

    private function actividadEliminable(string $dlOrg = 'dlb', string $tabla = 'dl'): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn(131001);
        $a->method('getDl_org')->willReturn($dlOrg);
        $a->method('getId_tabla')->willReturn($tabla);

        return $a;
    }

    private function permisoBorrar(bool $puede): void
    {
        $accion = $this->createMock(PermAccion::class);
        $accion->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => $puede && $p === PermAccionBits::BORRAR);
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('getPermisoActual')->willReturn($accion);
        $_SESSION['oPermActividades'] = $perm;
    }

    private function eliminar(?ActividadAll $actividad, BorrarActividad $borrar): ActividadEliminar
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->willReturn($actividad);

        return new ActividadEliminar($repo, $borrar);
    }

    public function test_sin_la_app_de_procesos_se_borra_sin_comprobar_permisos(): void
    {
        $this->procesosInstalado(false);
        $borrar = $this->createMock(BorrarActividad::class);
        $borrar->expects($this->exactly(3))->method('ejecutar')->willReturn('');

        $r = $this->eliminar($this->actividadEliminable(), $borrar)->execute(['sel' => ['3001#a', '3002'], 'id_activ' => '3003']);

        $this->assertSame('', $r);
    }

    public function test_eliminar_una_actividad_inexistente_avisa_y_no_borra(): void
    {
        $borrar = $this->createMock(BorrarActividad::class);
        $borrar->expects($this->never())->method('ejecutar');

        $this->assertNotSame('', $this->eliminar(null, $borrar)->execute(['id_activ' => '3001']));
    }

    public function test_con_procesos_instalados_hace_falta_la_sesion_de_permisos(): void
    {
        $this->procesosInstalado(true);
        $borrar = $this->createMock(BorrarActividad::class);
        $borrar->expects($this->never())->method('ejecutar');

        $this->assertNotSame('', $this->eliminar($this->actividadEliminable(), $borrar)->execute(['id_activ' => '3001']));
    }

    public function test_solo_se_borra_con_permiso_de_borrar(): void
    {
        $this->procesosInstalado(true);
        $this->permisoBorrar(true);
        $borrar = $this->createMock(BorrarActividad::class);
        $borrar->expects($this->once())->method('ejecutar')->with(3001)->willReturn('');

        $this->assertSame('', $this->eliminar($this->actividadEliminable(), $borrar)->execute(['id_activ' => '3001']));
    }

    public function test_sin_permiso_no_se_borra_la_propia_pero_si_se_quita_una_importada_de_otra_dl(): void
    {
        $this->procesosInstalado(true);
        $this->permisoBorrar(false);
        $borrar = $this->createMock(BorrarActividad::class);
        $borrar->expects($this->once())->method('ejecutar')->with(3002)->willReturn('');

        $propia = $this->eliminar($this->actividadEliminable('dlb', 'dl'), $borrar)->execute(['id_activ' => '3001']);
        $importada = $this->eliminar($this->actividadEliminable('dlp', 'dl'), $borrar)->execute(['id_activ' => '3002']);
        $otraTabla = $this->eliminar($this->actividadEliminable('dlp', 'cr'), $borrar)->execute(['id_activ' => '3003']);

        $this->assertNotSame('', $propia);
        $this->assertSame('', $importada);
        $this->assertNotSame('', $otraTabla);
    }

    // ---- cambiar tipo ----------------------------------------------------------------------

    private function cambiar(?ActividadAll $actividad, ?ActividadProcesoTareaRepositoryInterface $procesos = null, ?ActividadDlRepositoryInterface &$repo = null): ActividadCambiarTipo
    {
        $repo = $this->createMock(ActividadDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($actividad);

        return new ActividadCambiarTipo($repo, $procesos ?? $this->createMock(ActividadProcesoTareaRepositoryInterface::class));
    }

    /**
     * Hallazgo: los trozos del tipo se leen con `inputInt`, que convierte el «.» (sin elegir) en 0; el
     * `strpos('.')` posterior nunca se cumple, así que «debe seleccionar un tipo» es inalcanzable y se
     * compone un id incorrecto (aquí 1031).
     */
    public function test_un_tipo_sin_elegir_no_se_detecta_y_compone_un_id_erroneo(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->expects($this->once())->method('setId_tipo_activ')->with(1031);
        $uc = $this->cambiar($a, null, $repo);
        $repo->method('Guardar')->willReturn(true);

        $r = $uc->execute(['id_activ' => '3001', 'isfsv_val' => '1', 'iasistentes_val' => '.', 'iactividad_val' => '3', 'inom_tipo_val' => '1']);

        $this->assertArrayNotHasKey('tipo_error', $r);
    }

    public function test_cambiar_tipo_de_una_actividad_inexistente_avisa(): void
    {
        $this->assertNotSame('', $this->cambiar(null)->execute(['id_activ' => '3001', 'id_tipo_activ' => '131001'])['error_txt']);
    }

    public function test_cambiar_tipo_aplica_los_campos_y_regenera_el_proceso(): void
    {
        $this->procesosInstalado(true);
        $a = $this->createMock(ActividadAll::class);
        $a->expects($this->once())->method('setId_tipo_activ')->with(131001);
        $a->expects($this->once())->method('setDl_org')->with('dlb');
        $a->expects($this->once())->method('setNom_activ')->with('nuevo');
        $a->expects($this->once())->method('setPlazas')->with(30);
        $procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $procesos->expects($this->once())->method('generarProceso')->with('3001', null, true);
        $uc = $this->cambiar($a, $procesos, $repo);
        $repo->method('Guardar')->willReturn(true);

        $r = $uc->execute(['id_activ' => '3001', 'id_tipo_activ' => '131001', 'dl_org' => 'dlb#extra', 'nom_activ' => 'nuevo', 'plazas' => '30', 'f_ini' => '1/3/2026', 'h_ini' => '10:00']);

        $this->assertSame(['error_txt' => ''], $r);
    }

    public function test_el_tipo_se_compone_con_los_cuatro_trozos_si_no_viene_completo(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->expects($this->once())->method('setId_tipo_activ')->with(1310);
        $a->expects($this->once())->method('setDl_org')->with('');
        $uc = $this->cambiar($a, null, $repo);
        $repo->method('Guardar')->willReturn(true);

        $uc->execute(['id_activ' => '3001', 'isfsv_val' => '1', 'iasistentes_val' => '3', 'iactividad_val' => '1', 'inom_tipo_val' => '0']);
    }

    public function test_si_falla_el_guardado_no_se_regenera_el_proceso(): void
    {
        $this->procesosInstalado(true);
        $procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $procesos->expects($this->never())->method('generarProceso');
        $uc = $this->cambiar($this->createMock(ActividadAll::class), $procesos, $repo);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('sql');

        $this->assertStringContainsString('sql', $uc->execute(['id_activ' => '3001', 'id_tipo_activ' => '131001'])['error_txt']);
    }
}
