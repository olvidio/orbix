<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\web\Posicion;
use PHPUnit\Framework\TestCase;

/**
 * Cancelar en com_sacd_txt usa jsNavAtras(1). Sin enter() en esa pantalla,
 * la pila sigue en el padre (periodo) y no hay destino → el botón no hace nada.
 */
final class PosicionJsNavAtrasTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('posicion-jsnav-' . md5(static::class . (string) $this->name()));
        session_start();
        $_SESSION = [];
        session_write_close();
    }

    public function test_js_nav_atras_vacio_si_solo_esta_el_padre(): void
    {
        $padre = new Posicion('/com_sacd_activ_periodo.php', []);
        $padre->nav()->enter('/com_sacd_activ_periodo.php', '#main', [], []);

        $hija = new Posicion('/com_sacd_txt.php', []);
        $this->assertSame('', $hija->jsNavAtras(1));
    }

    public function test_js_nav_atras_tras_enter_en_la_hija_vuelve_al_padre(): void
    {
        $padre = new Posicion('/com_sacd_activ_periodo.php', []);
        $padre->nav()->enter('/com_sacd_activ_periodo.php', '#main', [], []);

        $hija = new Posicion('/com_sacd_txt.php', []);
        $hija->nav()->enter('/com_sacd_txt.php', '#main', [], []);

        $this->assertSame('fnjs_nav_atras(1);', $hija->jsNavAtras(1));
        $back = $hija->nav()->backTarget(1);
        $this->assertNotNull($back);
        $this->assertSame('/com_sacd_activ_periodo.php', $back['url']);
    }
}
