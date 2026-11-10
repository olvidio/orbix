<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\procesos\application\FasesActivCambioActividadTipoHtml;
use src\procesos\application\FasesActivCambioTipoActividadHtmlData;
use src\procesos\application\UsuarioPermActivData;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\entity\PermUsuarioActividad;
use src\shared\domain\criteria\Criteria;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Permisos de actividad por usuario/grupo (pantalla `usuario_perm_activ`) y selector de tipo de actividad de «cambio de fase». */
final class UsuarioPermActivDataTest extends myTest
{
    private GrupoRepositoryInterface&MockObject $grupos;
    private TipoDeActividadRepositoryInterface&MockObject $tipos;
    private ActividadFaseRepositoryInterface&MockObject $fases;
    private PermUsuarioActividadRepositoryInterface&MockObject $perms;
    private mixed $permAnterior;
    private mixed $sesionAnterior;
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        unset($_SESSION['oPerm'], $_SESSION['oConfig']);
        $_SESSION['session_auth'] = ['sfsv' => 1];
        $this->grupos = $this->createMock(GrupoRepositoryInterface::class);
        $this->tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $this->fases = $this->createMock(ActividadFaseRepositoryInterface::class);
        $this->perms = $this->createMock(PermUsuarioActividadRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach (['oPerm' => $this->permAnterior, 'session_auth' => $this->sesionAnterior, 'oConfig' => $this->configAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function permiso(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function uc(): UsuarioPermActivData
    {
        return new UsuarioPermActivData($this->grupos, $this->tipos, $this->fases, $this->perms);
    }

    private function permiso1(int $fase, int $afecta, int $on, int $off): PermUsuarioActividad&MockObject
    {
        $p = $this->createMock(PermUsuarioActividad::class);
        $p->method('getFase_ref')->willReturn($fase);
        $p->method('getAfecta_a')->willReturn($afecta);
        $p->method('getPerm_on')->willReturn($on);
        $p->method('getPerm_off')->willReturn($off);

        return $p;
    }

    public function test_sin_tipo_de_actividad_siempre_es_la_dl_propia(): void
    {
        $this->perms->method('matching')->willReturn([]);
        $this->tipos->expects($this->once())->method('getTiposDeProcesos')->with($this->anything(), true)->willReturn([]);

        $r = $this->uc()->execute(['id_usuario' => '55', 'id_tipo_activ_txt' => '', 'dl_propia' => 'f']);

        $this->assertSame('t', $r['dl_propia']);
    }

    public function test_el_nombre_viene_del_grupo_y_se_firma_el_guardado_con_su_id(): void
    {
        $g = $this->createMock(Grupo::class);
        $g->method('getUsuarioAsString')->willReturn('grupo-a');
        $this->grupos->method('findById')->with(55)->willReturn($g);
        $this->perms->method('matching')->willReturn([]);

        $r = $this->uc()->execute(['id_usuario' => '55']);

        $this->assertSame('grupo-a', $r['nombre']);
        $this->assertNotSame('', $r['ctx_guardar']);
        $this->assertCount(count(PermisosActividades::AFECTA), $r['aPerm'], 'una fila por cada tipo de afectación');
    }

    public function test_un_grupo_inexistente_deja_el_nombre_vacio(): void
    {
        $this->grupos->method('findById')->willReturn(null);
        $this->perms->method('matching')->willReturn([]);

        $this->assertSame('', $this->uc()->execute(['id_usuario' => '55'])['nombre']);
    }

    public function test_cada_tipo_de_afectacion_se_consulta_y_se_marca_solo_si_hay_permiso(): void
    {
        $criterios = [];
        $afecta = PermisosActividades::AFECTA;
        asort($afecta);
        $primero = (int) array_values($afecta)[0];
        $this->perms->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios, $primero): array {
            $f = DescribeCriteria::filtros($c);
            $criterios[] = $f;

            return in_array("afecta_a eq $primero", $f, true) ? [$this->permiso1(30, $primero, 3, 1)] : [];
        });

        $r = $this->uc()->execute(['id_usuario' => '55', 'id_tipo_activ_txt' => '131001', 'dl_propia' => 'true']);

        $this->assertCount(count($afecta), $criterios);
        $this->assertContains('id_usuario eq 55', $criterios[0]);
        $this->assertContains('dl_propia eq true', $criterios[0]);
        $this->assertSame([true, '30', '3', '1'], [$r['aPerm'][0]['marcado'], $r['aPerm'][0]['fase_ref'], $r['aPerm'][0]['perm_on'], $r['aPerm'][0]['perm_off']]);
        $this->assertSame([false, '', '', ''], [$r['aPerm'][1]['marcado'], $r['aPerm'][1]['fase_ref'], $r['aPerm'][1]['perm_on'], $r['aPerm'][1]['perm_off']]);
        $this->assertNotSame([], $r['a_acciones']);
    }

    /**
     * @dataProvider jefesProvider
     * @param list<string> $oficinas
     */
    public function test_ser_jefe_depende_de_oficinas_y_de_sfsv(array $oficinas, int $sfsv, bool $jefe): void
    {
        $_SESSION['session_auth'] = ['sfsv' => $sfsv];
        $this->permiso($oficinas);
        $this->perms->method('matching')->willReturn([]);

        $this->assertSame($jefe, $this->uc()->execute(['id_usuario' => '55'])['perm_jefe']);
    }

    /**
     * @return iterable<string, array{list<string>, int, bool}>
     */
    public static function jefesProvider(): iterable
    {
        yield 'sin permisos' => [[], 1, false];
        yield 'des en sv' => [[MenuDlPermissionBits::DES], 1, true];
        yield 'vcsd en sv' => [[MenuDlPermissionBits::VCSD], 1, true];
        yield 'des en sf no basta' => [[MenuDlPermissionBits::DES], 2, false];
        yield 'calendario en cualquiera' => [[MenuDlPermissionBits::CALENDARIO], 2, true];
    }

    public function test_el_jefe_de_calendario_de_la_configuracion_tambien_es_jefe(): void
    {
        $_SESSION['oConfig'] = new class {
            public function is_jefeCalendario(): bool
            {
                return true;
            }
        };
        $this->perms->method('matching')->willReturn([]);

        $this->assertTrue($this->uc()->execute(['id_usuario' => '55'])['perm_jefe']);
    }

    // ---- selector de tipo de actividad (cambio de fase) ----------------------------------------------------------------------

    public function test_el_selector_extendido_o_no_se_decide_por_sactividad2(): void
    {
        $render = new FasesActivCambioActividadTipoHtml();
        $uc = new FasesActivCambioTipoActividadHtmlData($render);

        $normal = $uc->execute(['id_tipo_activ' => '131001', 'sasistentes' => 'n', 'sactividad' => 'crt']);
        $ext = $uc->execute(['id_tipo_activ' => '131001', 'sasistentes' => 'n', 'sactividad2' => '01']);

        $this->assertIsString($normal['tipo_actividad_html']);
        $this->assertNotSame('', $normal['tipo_actividad_html']);
        $this->assertNotSame($normal['tipo_actividad_html'], $ext['tipo_actividad_html']);
    }

    public function test_con_permiso_de_calendario_el_selector_ofrece_todas_las_opciones(): void
    {
        $uc = new FasesActivCambioTipoActividadHtmlData(new FasesActivCambioActividadTipoHtml());

        $normal = $uc->execute(['id_tipo_activ' => '131001'])['tipo_actividad_html'];
        $_SESSION['session_auth'] = ['sfsv' => 2];
        $otroSfsv = $uc->execute(['id_tipo_activ' => '131001'])['tipo_actividad_html'];
        $this->permiso([MenuDlPermissionBits::CALENDARIO]);
        $jefe = $uc->execute(['id_tipo_activ' => '131001'])['tipo_actividad_html'];

        // Con un id de tipo ya elegido, sv/sf no cambian lo que se pinta; el modo «jefe» sí añade opciones.
        $this->assertSame($normal, $otroSfsv);
        $this->assertNotSame($normal, $jefe);
        $this->assertGreaterThan(strlen($normal), strlen($jefe));
    }
}
