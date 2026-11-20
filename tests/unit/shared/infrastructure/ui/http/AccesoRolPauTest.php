<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\ui\http\AccesoRolPau;

/**
 * Qué puede pedir desde el navegador un usuario con un rol limitado a una persona, casa o centro (rol «pau»:
 * `sacd`, `cdc`, `ctr`): las pantallas de sus menús, lo que estas piden por AJAX y lo común (inicio,
 * preferencias, avisos, contraseña y 2FA). Revisión de seguridad del 2026-11-18, punto 2. Antes llegaba a
 * cualquier pantalla y endpoint con solo tener sesión.
 */
final class AccesoRolPauTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_pau_' . bin2hex(random_bytes(4));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        rmdir($this->dir);
    }

    public function test_un_usuario_de_oficina_llega_a_todo(): void
    {
        $this->assertTrue(AccesoRolPau::permite('', 'frontend/personas/controller/personas_select.php'));
        $this->assertTrue(AccesoRolPau::permite('', 'src/usuarios/usuario_lista_data'));
    }

    public function test_lo_comun_para_cualquier_pau(): void
    {
        foreach (['sacd', 'cdc', 'ctr'] as $pau) {
            $this->assertTrue(AccesoRolPau::permite($pau, 'index.php'), $pau);
            $this->assertTrue(AccesoRolPau::permite($pau, 'frontend/usuarios/controller/preferencias.php'), $pau);
            $this->assertTrue(AccesoRolPau::permite($pau, 'src/usuarios/usuario_guardar_pwd'), $pau);
        }
    }

    public function test_cada_pau_llega_a_sus_pantallas(): void
    {
        $this->assertTrue(AccesoRolPau::permite('ctr', 'frontend/misas/controller/ver_plan_ctr.php'));
        $this->assertTrue(AccesoRolPau::permite('sacd', 'frontend/misas/controller/ver_plan_sacd.php'));
        $this->assertTrue(AccesoRolPau::permite('cdc', 'frontend/casas/controller/casa.php'));
        $this->assertTrue(AccesoRolPau::permite('cdc', 'src/actividadtarifas/tarifa_ubi_update'));
    }

    public function test_un_pau_no_llega_a_lo_de_otro_pau_ni_a_la_oficina(): void
    {
        $this->assertFalse(AccesoRolPau::permite('ctr', 'frontend/casas/controller/casa.php'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'frontend/misas/controller/ver_plan_sacd.php'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'frontend/personas/controller/personas_select.php'));
        $this->assertFalse(AccesoRolPau::permite('sacd', 'src/usuarios/usuario_lista_data'));
        $this->assertFalse(AccesoRolPau::permite('ctr', 'src/notas/persona_nota_eliminar'));
    }

    /** Del planning, la casa solo llega al de casas: no al de personas, centros ni zonas. */
    public function test_la_casa_solo_llega_al_planning_de_casas(): void
    {
        $this->assertTrue(AccesoRolPau::permite('cdc', 'frontend/planning/controller/planning_casa_select.php'));
        $this->assertTrue(AccesoRolPau::permite('cdc', 'src/planning/planning_casa_ver_data'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'frontend/planning/controller/planning_persona_select.php'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'src/planning/planning_ctr_select_data'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'frontend/planning/controller/planning_zones_que.php'));
    }

    /** Un pau que no está en la lista solo llega a lo común. */
    public function test_un_pau_desconocido_solo_lo_comun(): void
    {
        $this->assertTrue(AccesoRolPau::permite('nom', 'index.php'));
        $this->assertFalse(AccesoRolPau::permite('nom', 'frontend/misas/controller/ver_plan_ctr.php'));
    }

    /** Por el proxy AJAX (`src_ajax.php/src/...`) cuenta el endpoint al que se llama. */
    public function test_por_el_proxy_ajax_cuenta_el_endpoint(): void
    {
        $this->assertTrue(AccesoRolPau::permite('cdc', 'frontend/shared/controller/src_ajax.php/src/casas/casa_ingreso_update'));
        $this->assertFalse(AccesoRolPau::permite('cdc', 'frontend/shared/controller/src_ajax.php/src/notas/persona_nota_eliminar'));
    }

    public function test_en_modo_aviso_apunta_lo_denegado_y_deja_pasar(): void
    {
        $this->assertTrue(AccesoRolPau::decidir($this->dir, 'ctr', 'POST', '/orbix/src/notas/persona_nota_eliminar', ['ctx' => 'x'], false));

        $linea = (string) file_get_contents($this->dir . '/' . AccesoRolPau::REGISTRO_DENEGADO);
        $this->assertStringContainsString("ctr\tPOST\tsrc/notas/persona_nota_eliminar\tctx", $linea);
    }

    public function test_exigiendo_no_deja_pasar_y_lo_apunta(): void
    {
        $this->assertFalse(AccesoRolPau::decidir($this->dir, 'ctr', 'POST', '/orbix/src/notas/persona_nota_eliminar', [], true));
        $this->assertFileExists($this->dir . '/' . AccesoRolPau::REGISTRO_DENEGADO);
    }

    /**
     * La ruta que cuenta es la que va a despachar el enrutador, no la que aparenta la URL (revisión de
     * seguridad del 2026-11-19, AUT-04): `?r=/src/...` manda sobre el path, y los `..` se resuelven antes
     * de comparar con los patrones.
     *
     * @dataProvider rutasDisfrazadas
     */
    public function test_una_ruta_disfrazada_de_permitida_no_pasa(string $uri): void
    {
        $this->assertFalse(AccesoRolPau::decidir($this->dir, 'cdc', 'POST', $uri, [], true), $uri);
    }

    /** @return iterable<string, array{string}> */
    public static function rutasDisfrazadas(): iterable
    {
        yield 'r= sobre public/index.php' => ['/orbix/public/index.php?r=/src/usuarios/usuario_grupo_lst'];
        yield 'r= sobre index.php' => ['/orbix/index.php?r=/src/usuarios/usuario_grupo_lst'];
        yield 'segundo r en una ruta permitida' => ['/orbix/src/usuarios/usuario_preferencias?r=/src/usuarios/usuario_grupo_lst'];
        yield 'r= codificado' => ['/orbix/public/index.php?r=%2Fsrc%2Fusuarios%2Fusuario_grupo_lst'];
        yield '.. desde un patrón con comodín (src)' => ['/orbix/src/casas/../usuarios/usuario_grupo_lst'];
        yield '.. desde un patrón con comodín (frontend)' => ['/orbix/frontend/casas/controller/../../usuarios/controller/usuario_lista.php'];
        yield '.. codificado' => ['/orbix/src/casas/%2e%2e/usuarios/usuario_grupo_lst'];
    }

    public function test_la_ruta_de_r_permitida_sigue_pasando(): void
    {
        $this->assertTrue(AccesoRolPau::decidir($this->dir, 'cdc', 'POST', '/orbix/public/index.php?r=/src/usuarios/usuario_preferencias', [], true));
        $this->assertSame('src/usuarios/usuario_preferencias', AccesoRolPau::ruta('/orbix/public/index.php?dir=orbix&r=/src/usuarios/usuario_preferencias&x=1'));
        $this->assertSame('src/usuarios/usuario_grupo_lst', AccesoRolPau::ruta('/orbix/src/casas/../usuarios/usuario_grupo_lst'));
        $this->assertSame('index.php', AccesoRolPau::ruta('/orbix/index.php?a=1'));
    }

    public function test_lo_permitido_no_se_apunta(): void
    {
        $this->assertTrue(AccesoRolPau::decidir($this->dir, 'ctr', 'POST', '/orbix/frontend/misas/controller/ver_plan_ctr.php', [], true));
        $this->assertTrue(AccesoRolPau::decidir($this->dir, '', 'POST', '/orbix/src/notas/persona_nota_eliminar', [], true));
        $this->assertFileDoesNotExist($this->dir . '/' . AccesoRolPau::REGISTRO_DENEGADO);
    }

    /**
     * Lo que se comprueba es la ruta que el enrutador va a despachar, no la que aparenta la URL.
     */
    public function test_se_comprueba_la_ruta_despachada_y_no_la_url(): void
    {
        try {
            $this->assertSame('/orbix/public/index.php', AccesoRolPau::uriQueSeComprueba(['REQUEST_URI' => '/orbix/public/index.php']));
            AccesoRolPau::fijarRutaDespachada('/src/usuarios/usuario_lista');
            $uri = AccesoRolPau::uriQueSeComprueba(['REQUEST_URI' => '/orbix/public/index.php']);
            $this->assertSame('src/usuarios/usuario_lista', AccesoRolPau::ruta((string) $uri));
        } finally {
            AccesoRolPau::fijarRutaDespachada(null);
        }
    }

    public function test_los_enrutadores_no_toman_la_ruta_de_cabeceras_y_comprueban_la_que_despachan(): void
    {
        $raiz = dirname(__DIR__, 6);
        $publico = (string) file_get_contents($raiz . '/public/index.php');
        $this->assertStringNotContainsString('HTTP_X_ORIGINAL_URI', $publico);
        $this->assertStringNotContainsString('REDIRECT_URL', $publico);
        $this->assertStringContainsString('AccesoRolPau::fijarRutaDespachada(', $publico);

        $proxy = (string) file_get_contents($raiz . '/frontend/shared/controller/src_ajax.php');
        $this->assertStringContainsString('AccesoRolPau::fijarRutaDespachada($src)', $proxy);
        $this->assertStringContainsString('AccesoRolPau::comprobarPeticionActual()', $proxy);
    }

    public function test_con_el_cambio_de_contrasena_pendiente_solo_se_puede_cambiarla(): void
    {
        foreach ([
            'index.php', '', 'orbix',
            'frontend/usuarios/controller/usuario_form_pwd.php',
            'src/usuarios/usuario_guardar_pwd',
            'src/usuarios/usuario_guardar_pwd_form_data',
            'src/usuarios/usuario_check_pwd',
            'src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php',
        ] as $ruta) {
            $this->assertTrue(AccesoRolPau::permitidaConCambioDePasswordPendiente($ruta), $ruta);
        }
        foreach ([
            'frontend/usuarios/controller/usuario_lista.php',
            'src/usuarios/usuario_2fa_info',
            'src/usuarios/usuario_guardar',
            'src/personas/personas_select_datos',
            'src/usuarios/usuario_guardar_pwd/../usuario_guardar',
        ] as $ruta) {
            $this->assertFalse(AccesoRolPau::permitidaConCambioDePasswordPendiente($ruta), $ruta);
        }
    }
}
