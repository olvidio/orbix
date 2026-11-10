<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use src\procesos\domain\PermAccion;
use Tests\myTest;

/**
 * XPermisos es abstracta: se prueba con {@see PermAccion}, su implementación real
 * (nada=0, ocupado=1, ver=3, modificar=7, crear=15, borrar=31).
 */
final class XPermisosTest extends myTest
{
    // ---- comprobaciones de permiso ------------------------------------------

    /**
     * @dataProvider haveBitProvider
     */
    public function test_have_perm_bit_es_verdadero_si_comparten_algun_bit(int $acceso, int $bits, bool $esperado): void
    {
        $this->assertSame($esperado, (new PermAccion($acceso))->have_perm_bit($bits));
    }

    /**
     * @return iterable<string, array{int, int, bool}>
     */
    public static function haveBitProvider(): iterable
    {
        yield 'bit en común' => [7, 4, true];
        yield 'sin bits en común' => [7, 8, false];
        yield 'página sin bits' => [7, 0, false];
        yield 'usuario sin permisos' => [0, 31, false];
        yield 'basta uno de varios' => [1, 1 | 8, true];
    }

    /**
     * @dataProvider haveActivProvider
     */
    public function test_have_perm_activ_exige_todos_los_bits(int $acceso, string $perm, bool $esperado): void
    {
        $this->assertSame($esperado, (new PermAccion($acceso))->have_perm_activ($perm));
    }

    /**
     * @return iterable<string, array{int, string, bool}>
     */
    public static function haveActivProvider(): iterable
    {
        yield 'modificar permite ver' => [7, 'ver', true];
        yield 'modificar permite modificar' => [7, 'modificar', true];
        yield 'modificar no permite crear' => [7, 'crear', false];
        yield 'ver no permite modificar' => [3, 'modificar', false];
        yield 'borrar lo permite todo' => [31, 'crear', true];
        yield 'lista de permisos (suma de bits)' => [7, 'ver,modificar', true];
        yield 'lista con uno que no tiene' => [7, 'ver,crear', false];
        yield 'sin permisos no ve' => [0, 'ver', false];
        yield 'ocupado no basta para ver' => [1, 'ver', false];
    }

    /**
     * Seguridad por defecto: un nombre de permiso que no existe (error tipográfico, cadena
     * vacía…) deniega, nunca concede.
     *
     * @dataProvider permisoDesconocidoProvider
     */
    public function test_un_permiso_desconocido_o_vacio_deniega(string $perm): void
    {
        foreach ([0, 3, 31] as $acceso) {
            $p = new PermAccion($acceso);
            $this->assertFalse($p->have_perm_activ($perm), "have_perm_activ con acceso $acceso");
            $this->assertFalse($p->have_perm_action($perm), "have_perm_action con acceso $acceso");
            $this->assertFalse($p->only_perm($perm), "only_perm con acceso $acceso");
            $this->assertFalse($p->have_perm_oficina($perm), "have_perm_oficina con acceso $acceso");
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function permisoDesconocidoProvider(): iterable
    {
        yield 'nombre inexistente' => ['no_existe'];
        yield 'cadena vacía' => [''];
        yield 'error tipográfico' => ['modificr'];
        yield 'mayúsculas (los nombres son sensibles)' => ['VER'];
        yield 'con espacios' => [' ver'];
    }

    public function test_un_desconocido_en_una_lista_invalida_la_comprobacion_de_todos_los_bits(): void
    {
        $p = new PermAccion(31);

        $this->assertFalse($p->have_perm_activ('ver,modificr'), 'AND: un nombre mal escrito deniega aunque el otro valga');
        $this->assertFalse($p->have_perm_action('ver,modificr'));
        $this->assertFalse($p->only_perm('borrar,modificr'));
    }

    public function test_have_perm_oficina_con_lista_ignora_los_desconocidos_pero_sigue_siendo_cualquiera_de(): void
    {
        $this->assertTrue((new PermAccion(3))->have_perm_oficina('ver,no_existe'), 'basta que uno de los conocidos coincida');
        $this->assertFalse((new PermAccion(0))->have_perm_oficina('ver,no_existe'));
    }

    public function test_have_perm_action_se_comporta_como_have_perm_activ(): void
    {
        foreach ([0, 1, 3, 7, 15, 31] as $acceso) {
            foreach (['ver', 'modificar', 'crear', 'ver,borrar', 'nada'] as $perm) {
                $p = new PermAccion($acceso);
                $this->assertSame($p->have_perm_activ($perm), $p->have_perm_action($perm), "$acceso/$perm");
            }
        }
    }

    /**
     * @dataProvider haveOficinaProvider
     */
    public function test_have_perm_oficina_basta_con_un_bit(int $acceso, string $perm, bool $esperado): void
    {
        $this->assertSame($esperado, (new PermAccion($acceso))->have_perm_oficina($perm));
    }

    /**
     * @return iterable<string, array{int, string, bool}>
     */
    public static function haveOficinaProvider(): iterable
    {
        yield 'comparte algún bit' => [3, 'crear', true];
        yield 'no comparte' => [1, 'nada', false];
        yield 'lista: uno basta' => [1, 'ver,borrar', true];
        yield 'sin permisos' => [0, 'ver', false];
        yield 'permiso desconocido no concede' => [31, 'no_existe', false];
    }

    public function test_only_perm_exige_igualdad_exacta(): void
    {
        $this->assertTrue((new PermAccion(7))->only_perm('modificar'));
        $this->assertTrue((new PermAccion(7))->only_perm('ver,modificar'));
        $this->assertFalse((new PermAccion(7))->only_perm('ver'));
        $this->assertFalse((new PermAccion(31))->only_perm('modificar'));
        $this->assertTrue((new PermAccion(0))->only_perm('nada'));
        $this->assertFalse((new PermAccion(0))->only_perm('no_existe'), 'antes: 0 === 0 daba true');
    }

    // ---- sumas de permisos ------------------------------------------------------

    public function test_permsum_suma_los_bits_e_ignora_los_desconocidos(): void
    {
        $p = new PermAccion(0);

        $this->assertSame([false, 15], $p->permsum(['ver', 'zzz', 'crear']), 'ok=false avisa de que hubo un nombre desconocido');
        $this->assertSame([true, 15], $p->permsum(['ver', 'crear']));
        $this->assertSame([true, 0], $p->permsum([]));
        $this->assertSame([false, 0], $p->permsum(['zzz']));
    }

    public function test_permsum_bit_hace_or(): void
    {
        $p = new PermAccion(0);

        $this->assertSame([true, 7], $p->permsum_bit([1, 2, 4]));
        $this->assertSame([true, 3], $p->permsum_bit([3, 1]));
        $this->assertSame([true, 0], $p->permsum_bit([]));
    }

    public function test_permxor_hace_xor_y_escribe_cada_valor(): void
    {
        $p = new PermAccion(0);

        ob_start();
        $r = $p->permxor([3, 1]);
        $salida = (string) ob_get_clean();

        $this->assertSame([true, 2], $r);
        $this->assertSame("val: 3 :: 0<br>val: 1 :: 1<br>", $salida);
    }

    public function test_perm_islisted(): void
    {
        $p = new PermAccion(0);

        $this->assertTrue($p->perm_islisted('a,b,c', 'b'));
        $this->assertFalse($p->perm_islisted('a,bb,c', 'b'), 'comparación exacta, no por subcadena');
        $this->assertFalse($p->perm_islisted('', 'b'));
        $this->assertTrue($p->perm_islisted('', ''));
    }

    // ---- accesores ------------------------------------------------------------------

    public function test_get_y_set_permissions(): void
    {
        $p = new PermAccion(0);
        $this->assertSame(['nada' => 0, 'ocupado' => 1, 'ver' => 3, 'modificar' => 7, 'crear' => 15, 'borrar' => 31], $p->getPermissions());

        $p->setPermissions(['x' => 1, 'y' => 2]);

        $this->assertSame(['x' => 1, 'y' => 2], $p->getPermissions());
        $this->assertSame([true, 3], $p->permsum(['x', 'y']));
    }

    public function test_set_accion_cambia_el_permiso_del_usuario(): void
    {
        $p = new PermAccion(0);
        $this->assertFalse($p->have_perm_activ('ver'));

        $p->setAccion(3);

        $this->assertTrue($p->have_perm_activ('ver'));
        $this->assertFalse($p->have_perm_activ('modificar'));
    }

    // ---- textos y listas -----------------------------------------------------------

    public function test_lista_txt_solo_valor_exacto(): void
    {
        $p = new PermAccion(0);

        $this->assertSame('modificar', $p->lista_txt(7));
        $this->assertSame('nada', $p->lista_txt(0));
        $this->assertSame('', $p->lista_txt(5));
    }

    public function test_lista_txt2_incluye_nada_siempre_y_los_subconjuntos(): void
    {
        $p = new PermAccion(0);

        $this->assertSame('nada, ocupado, ver, modificar', $p->lista_txt2(7));
        $this->assertSame('nada', $p->lista_txt2(0));
    }

    public function test_lista_tiene_txt_lista_los_que_comparten_bit(): void
    {
        $p = new PermAccion(0);

        // 7 comparte algún bit con ocupado(1), ver(3), modificar(7), crear(15) y borrar(31).
        $this->assertSame('ocupado, ver, modificar, crear, borrar', $p->lista_tiene_txt(7));
        $this->assertSame('', $p->lista_tiene_txt(0));
    }

    public function test_lista_array_devuelve_siempre_todos_los_permisos_ignorando_el_valor(): void
    {
        $esperado = [0 => 'nada', 1 => 'ocupado', 3 => 'ver', 7 => 'modificar', 15 => 'crear', 31 => 'borrar'];
        $p = new PermAccion(0);

        $this->assertSame($esperado, $p->lista_array());
        $this->assertSame($esperado, $p->lista_array(7), 'el argumento $bin no filtra');
    }

    // ---- HTML -------------------------------------------------------------------------

    public function test_perm_sel_marca_los_seleccionados(): void
    {
        $html = (new PermAccion(0))->perm_sel('permisos', 'ver,crear');

        $this->assertStringStartsWith('<select multiple name="permisos[]">', $html);
        $this->assertStringEndsWith('</select>', $html);
        $this->assertStringContainsString("<option selected>ver\n", $html);
        $this->assertStringContainsString("<option selected>crear\n", $html);
        $this->assertStringContainsString("<option>modificar\n", $html);
        $this->assertSame(2, substr_count($html, 'selected'));
        $this->assertSame(6, substr_count($html, '<option'));
    }

    public function test_perm_sel_con_clase(): void
    {
        $html = (new PermAccion(0))->perm_sel('p', '', 'ancho');

        $this->assertStringContainsString('name="p[]" class=ancho>', $html);
        $this->assertSame(6, substr_count($html, '<option class=ancho>'));
    }

    public function test_cuadros_radio_marca_solo_el_valor_exacto(): void
    {
        $html = (new PermAccion(0))->cuadros_radio('perm', 7);

        $this->assertSame(1, substr_count($html, 'checked'));
        $this->assertStringContainsString('value="7" checked>modificar', $html);
        $this->assertSame(6, substr_count($html, 'type="radio"'));
        $this->assertStringContainsString('name="perm[]"', $html);
    }

    public function test_cuadros_radio_con_cero_marca_nada(): void
    {
        $html = (new PermAccion(0))->cuadros_radio('perm', 0);

        $this->assertStringContainsString('value="0" checked>nada', $html);
        $this->assertSame(1, substr_count($html, 'checked'));
    }

    public function test_cuadros_check_con_valor_exacto_marca_solo_ese(): void
    {
        $html = (new PermAccion(0))->cuadros_check('perm', 7);

        $this->assertSame(1, substr_count($html, 'checked'));
        $this->assertStringContainsString('value="7" checked>modificar', $html);
    }

    public function test_cuadros_check_sin_valor_exacto_marca_los_que_comparten_bit(): void
    {
        $html = (new PermAccion(0))->cuadros_check('perm', 5);

        // 5 & {1,3,7,15,31} es distinto de 0; 5 & 0 es 0.
        $this->assertSame(5, substr_count($html, 'checked'));
        $this->assertStringNotContainsString('value="0" checked', $html);
    }

    public function test_cuadros_check_cero_no_marca_ninguno(): void
    {
        $this->assertSame(0, substr_count((new PermAccion(0))->cuadros_check('perm', 0), 'checked'));
    }

    public function test_cuadros_check_menu_marca_por_valor_y_escribe_los_nombres(): void
    {
        ob_start();
        $html = (new PermAccion(0))->cuadros_check_menu('perm', [3, 15]);
        $salida = (string) ob_get_clean();

        $this->assertSame(2, substr_count($html, 'checked'));
        $this->assertStringContainsString('value="ver" checked>ver', $html);
        $this->assertStringContainsString('value="crear" checked>crear', $html);
        $this->assertSame('nada<br>ocupado<br>ver<br>modificar<br>crear<br>borrar<br>', $salida);
    }

    public function test_cuadros_check_read_usa_iconos_segun_el_bit(): void
    {
        $html = (new PermAccion(0))->cuadros_check_read(7);

        // 7 & {0,1,3,7,15,31}: 1, 3 y 7 sí; 0, 15 y 31 no… 7&15=7, 7&31=7 → también sí.
        $this->assertSame(5, substr_count($html, 'checkbox-checked.png'));
        $this->assertSame(1, substr_count($html, 'check-box-outline-blank.png'));
        $this->assertStringContainsString('>nada', $html);
    }
}
