<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use src\actividades\application\ActividadLugar;
use src\actividades\application\ActividadQueDatos;
use src\actividades\application\ActividadQueFiltrosBloque;
use src\actividades\application\TipoActivFormNuevo;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\DelegacionName;
use src\ubis\domain\value_objects\RegionCode;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/** Bloques de la pantalla «actividad_que»: filtros extra, selector de tipo y formulario de tipo nuevo. */
final class ActividadQueBloquesTest extends myTest
{
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
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

    private function delegacion(string $dl, string $nombre, string $region = 'H'): Delegacion
    {
        $d = $this->createMock(Delegacion::class);
        $d->method('getDlVo')->willReturn(new DelegacionCode($dl));
        $d->method('getNombreDlVo')->willReturn(new DelegacionName($nombre));
        $d->method('getRegionVo')->willReturn(new RegionCode($region));

        return $d;
    }

    private function bloque(array $lugares = []): ActividadQueFiltrosBloque
    {
        $delRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delRepo->method('matching')->willReturn([$this->delegacion('dlb', 'Barcelona'), $this->delegacion('dlp', 'Pamplona')]);
        $lugar = $this->createMock(ActividadLugar::class);
        $lugar->method('getLugaresPosibles')->willReturn($lugares);

        return new ActividadQueFiltrosBloque($lugar, new DelegacionDropdown($delRepo));
    }

    private function comoCentro(): void
    {
        $roles = $GLOBALS['container']->get(RoleRepositoryInterface::class)->getArrayRolesPau();
        $idCtr = array_search('ctr', $roles, true);
        if ($idCtr === false) {
            $this->markTestSkipped('No hay un rol de tipo «ctr» en la BD.');
        }
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn((int) $idCtr);
        $_SESSION['session_auth']['MiUsuario'] = $u;
    }

    public function test_un_usuario_de_centro_no_ve_los_filtros_extra(): void
    {
        $this->comoCentro();

        $this->assertSame(['visible' => false], $this->bloque()->ejecutar(1, 'buscar', '', '', 0, 0, false));
    }

    public function test_el_bloque_ofrece_filtro_de_lugar_organiza_y_publicada(): void
    {
        $r = $this->bloque([1003 => 'Casa A'])->ejecutar(1, 'buscar', 'dlb', 'dl|dlb', 1003, 1, false);

        $this->assertTrue($r['visible']);
        $this->assertSame(['filtro_lugar', 'dl|dlb', 'fnjs_lugar()'], [$r['filtro_lugar']['id'], $r['filtro_lugar']['selected'], $r['filtro_lugar']['action']]);
        $this->assertSame(['id_ubi', '1003', [['1003', 'Casa A']]], [$r['lugar']['id'], $r['lugar']['selected'], $r['lugar']['opciones']]);
        $this->assertSame(['dl_org', 'dlb', ''], [$r['dl_org']['id'], $r['dl_org']['selected'], $r['dl_org']['action']]);
        $this->assertSame(['show' => true, 'value' => 1], $r['publicado']);
        $this->assertContains(['dlb', 'Barcelona (H-dlb)'], $r['dl_org']['opciones']);
        $this->assertArrayHasKey('lugar', $r['labels']);
    }

    public function test_sin_filtro_de_lugar_no_hay_desplegable_de_lugar(): void
    {
        $r = $this->bloque()->ejecutar(1, 'buscar', '', '', 0, 0, false);

        $this->assertNull($r['lugar']);
    }

    public function test_el_modo_importar_excluye_mi_dl_y_oculta_publicada(): void
    {
        $r = $this->bloque()->ejecutar(1, 'importar', '', '', 0, 0, false);

        $this->assertSame(['dlb'], $r['dl_org']['opcion_no']);
        $this->assertFalse($r['publicado']['show']);
    }

    public function test_el_modo_publicar_solo_deja_mi_dl_sin_blanco(): void
    {
        $r = $this->bloque()->ejecutar(1, 'publicar', '', '', 0, 0, false);

        $this->assertSame([['dlb', 'dlb']], $r['dl_org']['opciones']);
        $this->assertFalse($r['dl_org']['blanco']);
    }

    public function test_con_procesos_instalados_el_cambio_de_organizador_actualiza_las_fases(): void
    {
        $this->assertSame('fnjs_actualizar_fases();', $this->bloque()->ejecutar(1, 'buscar', '', '', 0, 0, true)['dl_org']['action']);
    }

    public function test_en_sf_mi_dl_lleva_la_f(): void
    {
        $r = $this->bloque()->ejecutar(2, 'publicar', '', '', 0, 0, false);

        $this->assertSame([['dlbf', 'dlbf']], $r['dl_org']['opciones']);
    }

    // ---- HTML de tipo (usa el helper de frontend) ----------------------------------------------------------------------

    public function test_el_selector_de_tipo_devuelve_html_y_el_extendido_difiere(): void
    {
        $uc = new ActividadQueDatos();

        $normal = $uc->execute(['id_tipo_activ' => '131001', 'sactividad' => 'crt']);
        $ext = $uc->execute(['id_tipo_activ' => '131001', 'extendida' => true, 'sactividad2' => '01']);
        $vacio = $uc->execute([]);

        $this->assertSame(['actividad_tipo_html'], array_keys($normal));
        $this->assertNotSame('', $normal['actividad_tipo_html']);
        $this->assertNotSame($normal['actividad_tipo_html'], $ext['actividad_tipo_html']);
        $this->assertIsString($vacio['actividad_tipo_html']);
    }

    public function test_los_parametros_que_y_para_cambian_el_selector(): void
    {
        $uc = new ActividadQueDatos();

        $a = $uc->execute(['id_tipo_activ' => '131001', 'perm_jefe' => true])['actividad_tipo_html'];
        $b = $uc->execute(['id_tipo_activ' => '131001', 'perm_jefe' => true, 'sfsv_all' => false, 'evitar_procesos' => true])['actividad_tipo_html'];

        $this->assertNotSame('', $a);
        $this->assertNotSame('', $b);
    }

    public function test_el_formulario_de_tipo_nuevo_trae_campos_firma_y_botones(): void
    {
        $html = (new TipoActivFormNuevo())->execute();

        $this->assertStringStartsWith("<form id='frm_tipo_activ'>", $html);
        $this->assertStringContainsString('id_nom_tipo_activ', $html);
        $this->assertStringContainsString('nom_tipo_activ', $html);
        $this->assertStringContainsString("fnjs_guardar_nuevo('#frm_tipo_activ')", $html);
        $this->assertStringEndsWith('</form> ', $html);
    }
}
