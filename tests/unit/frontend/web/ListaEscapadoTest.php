<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\web\Lista;
use Tests\myTest;

/**
 * `Lista` pinta datos de la BD (nombres de actividades, personas, centros...): una celda es texto. El HTML
 * que monta el propio código para una celda se marca aparte con `['html' => ...]` (revisión de seguridad
 * del 2026-11-20, XSS-02 y XSS-08).
 */
final class ListaEscapadoTest extends myTest
{
    private const MALO = 'zz"\'><img src=x onerror=alert(1)>';

    private function tabla(array $datos): Lista
    {
        $t = new Lista();
        $t->setId_tabla('zz_tabla');
        $t->setCabeceras(['nombre', 'otra']);
        $t->setDatos($datos);

        return $t;
    }

    public function test_una_celda_es_texto_en_la_tabla_html_y_en_la_lista_simple(): void
    {
        foreach (['mostrar_tabla_html', 'lista'] as $metodo) {
            $html = $this->tabla([[1 => self::MALO, 2 => 'a &amp; b &nbsp;']])->$metodo();

            $this->assertStringNotContainsString('<img', $html, $metodo);
            $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html, $metodo);
            $this->assertStringContainsString('a &amp; b &nbsp;', $html, "$metodo: las entidades ya escritas se respetan");
        }
    }

    public function test_el_texto_de_una_celda_con_enlace_tambien_y_el_enlace_no_sale_de_su_atributo(): void
    {
        $html = $this->tabla([[1 => ['valor' => self::MALO, 'ira' => 'x.php?a=1&b="><x-aud>'], 2 => ['valor' => self::MALO, 'script' => "fnjs_x('a'><x-aud>)"]]])->mostrar_tabla_html();

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<x-aud', $html);
        $this->assertSame(2, substr_count($html, 'class="link"'));
    }

    public function test_el_html_que_monta_el_codigo_se_marca_y_sale_tal_cual(): void
    {
        $html = $this->tabla([[1 => ['html' => '<span class="link" onclick="fnjs_modificar(7)">modificar</span>'], 2 => 'x']])->mostrar_tabla_html();

        $this->assertStringContainsString('<td><span class="link" onclick="fnjs_modificar(7)">modificar</span></td>', $html);
    }

    public function test_el_valor_de_la_casilla_y_el_scroll_no_salen_de_su_atributo(): void
    {
        $html = $this->tabla([['sel' => "7'><x-aud>", 'scroll_id' => '"><x-aud>', 1 => 'a', 2 => 'b']])->mostrar_tabla_html();

        $this->assertStringNotContainsString('<x-aud', $html);
    }

    public function test_la_rejilla_escapa_el_texto_de_las_columnas_con_enlace(): void
    {
        $codigo = (string) file_get_contents(dirname(__DIR__, 4) . '/frontend/shared/web/Lista.php');

        $this->assertStringNotContainsString('\\" >\\"+value+\\"</span>\\"', $codigo);
        $this->assertStringContainsString('fnjs_lista_esc(value)', $codigo);
    }

    public function test_el_scroll_que_llega_en_la_peticion_es_un_numero(): void
    {
        $antes = $_POST;
        try {
            $_POST = ['scroll_id_x' => '"><img src=x onerror=1>'];
            $this->assertSame('', \frontend\shared\helpers\ListNavSupport::scrollIdFromPost());
            $_POST = ['scroll_id_x' => '37'];
            $this->assertSame('37', \frontend\shared\helpers\ListNavSupport::scrollIdFromPost());
        } finally {
            $_POST = $antes;
        }
    }
}
