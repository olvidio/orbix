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

    public function test_lo_permitido_no_se_apunta(): void
    {
        $this->assertTrue(AccesoRolPau::decidir($this->dir, 'ctr', 'POST', '/orbix/frontend/misas/controller/ver_plan_ctr.php', [], true));
        $this->assertTrue(AccesoRolPau::decidir($this->dir, '', 'POST', '/orbix/src/notas/persona_nota_eliminar', [], true));
        $this->assertFileDoesNotExist($this->dir . '/' . AccesoRolPau::REGISTRO_DENEGADO);
    }
}
