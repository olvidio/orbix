<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ActividadEditar;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\procesos\domain\PermAccionBits;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;

/** Guardado de la edición de una actividad: permisos, tipo, lugar, organizador, proceso y plazas. */
final class ActividadEditarTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $repo;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadPlazasRepositoryInterface&MockObject $plazas;
    private ActividadPlazasDlRepositoryInterface&MockObject $plazasDl;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPermActividades', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
            unset($_SESSION[$k]);
        }
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->plazas = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $this->plazasDl = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
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

    private function uc(): ActividadEditar
    {
        return new ActividadEditar($this->repo, $this->procesos, $this->delegaciones, $this->plazas, $this->plazasDl);
    }

    private function apps(array $nombres): void
    {
        $ids = ['procesos' => 3, 'actividadplazas' => 4];
        $_SESSION['config'] = ['a_apps' => array_intersect_key($ids, array_flip($nombres)), 'app_installed' => array_values(array_intersect_key($ids, array_flip($nombres)))];
    }

    private function permisos(bool $crear): void
    {
        $accion = $this->createMock(PermAccion::class);
        $accion->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => $crear && $p === PermAccionBits::CREAR);
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('getPermisoActual')->willReturn($accion);
        $_SESSION['oPermActividades'] = $perm;
    }

    private function actividad(?string $dlOrg = 'dlp', ?int $plazas = 20): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getDl_org')->willReturn($dlOrg);
        $a->method('getPlazas')->willReturn($plazas);
        $a->method('getId_activ')->willReturn(3001);

        return $a;
    }

    public function test_sin_sesion_de_permisos_no_se_edita(): void
    {
        $this->repo->expects($this->never())->method('findById');

        $this->assertNotSame('', $this->uc()->execute(['id_activ' => '3001'])['error_txt']);
    }

    public function test_una_actividad_inexistente_avisa(): void
    {
        $this->permisos(false);
        $this->repo->method('findById')->willReturn(null);

        $this->assertNotSame('', $this->uc()->execute(['id_activ' => '3001'])['error_txt']);
    }

    /**
     * Hallazgo: con permiso de crear y procesos instalados, el tipo se compone de cuatro trozos leídos con
     * `inputInt`, que convierte el «.» (sin elegir) en 0: el aviso «debe seleccionar un tipo» es inalcanzable.
     */
    public function test_con_permiso_de_crear_el_tipo_se_compone_de_los_cuatro_trozos(): void
    {
        $this->apps(['procesos']);
        $this->permisos(true);
        $a = $this->actividad();
        $a->expects($this->once())->method('setId_tipo_activ')->with(131001);
        $this->repo->method('findById')->willReturn($a);
        $this->repo->method('Guardar')->willReturn(true);

        $r = $this->uc()->execute(['id_activ' => '3001', 'isfsv_val' => '1', 'iasistentes_val' => '3', 'iactividad_val' => '1', 'inom_tipo_val' => '001']);

        $this->assertSame(['error_txt' => ''], $r);
    }

    public function test_sin_permiso_de_crear_se_usa_el_tipo_recibido_y_uno_corto_se_ignora(): void
    {
        $this->permisos(false);
        $a = $this->actividad();
        $a->expects($this->once())->method('setId_tipo_activ')->with(131001);
        $b = $this->actividad();
        $b->expects($this->never())->method('setId_tipo_activ');
        $this->repo->method('findById')->willReturnOnConsecutiveCalls($a, $b);
        $this->repo->method('Guardar')->willReturn(true);
        $uc = $this->uc();

        $uc->execute(['id_activ' => '3001', 'id_tipo_activ' => '131001']);
        $uc->execute(['id_activ' => '3001', 'id_tipo_activ' => '1310']);
    }

    /**
     * @dataProvider lugaresProvider
     */
    public function test_el_lugar_especifico_solo_se_guarda_si_no_hay_ubi_concreta(int $idUbi, string $lugarEsp, string $esperado): void
    {
        $this->permisos(false);
        $a = $this->actividad();
        $a->expects($this->once())->method('setId_ubi')->with($idUbi);
        $a->expects($this->once())->method('setLugar_esp')->with($esperado);
        $this->repo->method('findById')->willReturn($a);
        $this->repo->method('Guardar')->willReturn(true);

        $this->uc()->execute(['id_activ' => '3001', 'id_ubi' => (string) $idUbi, 'lugar_esp' => $lugarEsp]);
    }

    /**
     * @return iterable<string, array{int, string, string}>
     */
    public static function lugaresProvider(): iterable
    {
        yield 'casa concreta' => [1003, 'texto', ''];
        yield 'sin ubi' => [0, 'texto', 'texto'];
        yield 'ubi 1 = otro lugar' => [1, 'en un parque', 'en un parque'];
    }

    public function test_un_fallo_de_guardado_se_devuelve_sin_regenerar_nada(): void
    {
        $this->apps(['procesos']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad());
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('sql');
        $this->procesos->expects($this->never())->method('generarProceso');

        $this->assertStringContainsString('sql', $this->uc()->execute(['id_activ' => '3001'])['error_txt']);
    }

    public function test_si_la_actividad_pasa_a_ser_de_mi_dl_se_regenera_el_proceso(): void
    {
        $this->apps(['procesos']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlp'));
        $this->repo->method('Guardar')->willReturn(true);
        $this->procesos->expects($this->once())->method('generarProceso')->with('3001', null, false, $this->anything());

        $this->uc()->execute(['id_activ' => '3001', 'dl_org' => 'dlb#x']);
    }

    public function test_si_el_organizador_no_cambia_o_no_afecta_a_mi_dl_no_se_regenera(): void
    {
        $this->apps(['procesos']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturnOnConsecutiveCalls($this->actividad('dlp'), $this->actividad('dlp'));
        $this->repo->method('Guardar')->willReturn(true);
        $this->procesos->expects($this->never())->method('generarProceso');
        $uc = $this->uc();

        $uc->execute(['id_activ' => '3001', 'dl_org' => 'dlp']);
        $uc->execute(['id_activ' => '3001', 'dl_org' => 'dlx']);
    }

    public function test_al_cambiar_las_plazas_de_mi_dl_se_actualiza_la_tabla_de_plazas_de_la_dl(): void
    {
        $this->apps(['actividadplazas']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlb', 20));
        $this->repo->method('Guardar')->willReturn(true);
        $this->delegaciones->method('matching')->willReturn([]);
        $this->plazas->method('matching')->willReturn([]);
        $pl = $this->createMock(ActividadPlazas::class);
        $pl->expects($this->once())->method('setPlazas')->with(30);
        $this->plazas->method('findById')->with(3001)->willReturn($pl);
        $this->plazasDl->expects($this->once())->method('Guardar')->with($pl)->willReturn(true);

        $this->assertSame('', $this->uc()->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '30'])['error_txt']);
    }

    public function test_si_la_dl_ya_habia_cambiado_las_plazas_por_su_cuenta_no_se_pisan(): void
    {
        $this->apps(['actividadplazas']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlb', 20));
        $this->repo->method('Guardar')->willReturn(true);
        $this->delegaciones->method('matching')->willReturn([]);
        $propia = $this->createMock(ActividadPlazas::class);
        $propia->method('getPlazas')->willReturn('25');
        $this->plazas->method('matching')->willReturn([$propia]);
        $this->plazasDl->expects($this->never())->method('Guardar');

        $this->uc()->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '30']);
    }

    public function test_sin_cambio_de_plazas_o_de_otra_dl_no_se_toca_la_tabla_de_plazas(): void
    {
        $this->apps(['actividadplazas']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlb', 20));
        $this->repo->method('Guardar')->willReturn(true);
        $this->plazas->expects($this->never())->method('findById');
        $uc = $this->uc();

        $uc->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '20']);
        $uc->execute(['id_activ' => '3001', 'dl_org' => 'dlp', 'plazas' => '30']);
        $uc->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '0']);
    }

    public function test_un_error_al_guardar_las_plazas_de_la_dl_se_acumula(): void
    {
        $this->apps(['actividadplazas']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlb', 20));
        $this->repo->method('Guardar')->willReturn(true);
        $this->delegaciones->method('matching')->willReturn([]);
        $this->plazas->method('matching')->willReturn([]);
        $this->plazas->method('findById')->willReturn($this->createMock(ActividadPlazas::class));
        $this->plazasDl->method('Guardar')->willReturn(false);
        $this->plazasDl->method('getErrorTxt')->willReturn('fk');

        $this->assertStringContainsString('fk', $this->uc()->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '30'])['error_txt']);
    }

    /**
     * Hallazgo: `ActividadPlazas::getPlazas()` devuelve `?string` y `$plazas_old` es `?int`; la comparación
     * estricta `!==` es siempre verdadera, así que si la dl ya tiene una fila de plazas nunca se actualiza,
     * aunque su valor coincida con el anterior (el caso en el que sí debería actualizarse).
     */
    public function test_la_fila_de_plazas_de_la_dl_nunca_se_actualiza_por_comparar_string_con_int(): void
    {
        $this->apps(['actividadplazas']);
        $this->permisos(false);
        $this->repo->method('findById')->willReturn($this->actividad('dlb', 20));
        $this->repo->method('Guardar')->willReturn(true);
        $this->delegaciones->method('matching')->willReturn([]);
        $igualQueAntes = $this->createMock(ActividadPlazas::class);
        $igualQueAntes->method('getPlazas')->willReturn('20');
        $this->plazas->method('matching')->willReturn([$igualQueAntes]);
        $this->plazasDl->expects($this->never())->method('Guardar');

        $this->uc()->execute(['id_activ' => '3001', 'dl_org' => 'dlb', 'plazas' => '30']);
    }
}
