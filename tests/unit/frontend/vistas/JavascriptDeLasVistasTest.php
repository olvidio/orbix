<?php

declare(strict_types=1);

namespace Tests\unit\frontend\vistas;

use frontend\shared\helpers\JsEscape;
use PHPUnit\Framework\TestCase;

/**
 * El JavaScript de las pantallas no convierte datos en HTML ni en código (revisión de seguridad del
 * 2026-11-20, área 8): lo que llega de la BD o de una respuesta se pone como texto, y lo que PHP escribe
 * dentro de un `<script>` va codificado para JavaScript.
 */
final class JavascriptDeLasVistasTest extends TestCase
{
    private const MALO = "a'b\"c</script><!--<script>\\d\n";

    private function codigo(string $ruta): string
    {
        return (string) file_get_contents(dirname(__DIR__, 4) . '/' . $ruta);
    }

    public function test_el_contenido_de_una_cadena_js_no_puede_cerrarla_ni_cerrar_el_script(): void
    {
        $js = JsEscape::cadena(self::MALO);

        $this->assertDoesNotMatchRegularExpression('~[\'"<>\n]~', $js);
        $this->assertSame(self::MALO, json_decode('"' . $js . '"'), 'el valor es el mismo');
        $this->assertSame('H-dlbv', JsEscape::cadena('H-dlbv'));
    }

    public function test_el_json_para_un_script_no_lleva_signos_de_html(): void
    {
        $json = JsEscape::json(['t' => self::MALO, 'n' => 3, 'ñ' => 'año']);

        $this->assertDoesNotMatchRegularExpression('~[<>&\']~', $json);
        $this->assertSame(['t' => self::MALO, 'n' => 3, 'ñ' => 'año'], json_decode($json, true));
    }

    public function test_las_rejillas_editables_escapan_el_valor_de_la_celda(): void
    {
        $codigo = $this->codigo('frontend/shared/web/TablaEditable.php');

        $this->assertStringNotContainsString('+value+', $codigo);
        $this->assertGreaterThanOrEqual(7, substr_count($codigo, 'fnjs_lista_esc(value)'));
        $this->assertDoesNotMatchRegularExpression('/scalarString\(\$aPrefs\[\'(width|height)Grid\'\]/', $codigo, 'el ancho y el alto de las preferencias son números');
    }

    public function test_el_autocompletado_del_acta_pinta_la_sugerencia_como_texto(): void
    {
        $codigo = $this->codigo('frontend/notas/view/acta_ver.phtml');

        $this->assertStringNotContainsString('"<div>" + item.label', $codigo);
        $this->assertSame(2, substr_count($codigo, '$("<div>").text(item.label)'));
    }

    public function test_las_cuadriculas_de_misas_no_meten_datos_como_html(): void
    {
        foreach ([
            'frontend/misas/view/modificar_cuadricula_zona.phtml',
            'frontend/misas/view/ver_encargos_zona.phtml',
            'frontend/misas/view/ver_iniciales_zona.phtml',
            'frontend/zonassacd/view/zona_sacd.phtml',
        ] as $vista) {
            $codigo = $this->codigo($vista);
            $this->assertDoesNotMatchRegularExpression('/#titulo_modal\'\)\.html\(/', $codigo, $vista);
            $this->assertDoesNotMatchRegularExpression('/\{\s*text:\s*value\b/', $codigo, $vista);
        }
        $this->assertStringNotContainsString('$(id_nom).html(nombre_new)', $this->codigo('frontend/encargossacd/view/propuestas_lista.html.twig'));
    }

    public function test_el_json_de_las_cuadriculas_y_del_menu_va_codificado_para_script(): void
    {
        foreach ([
            'frontend/misas/controller/ver_encargos_zona.php',
            'frontend/misas/controller/ver_iniciales_zona.php',
            'frontend/misas/controller/ver_encargos_centros.php',
        ] as $fichero) {
            $this->assertSame(2, substr_count($this->codigo($fichero), 'JsEscape::json('), $fichero);
        }
        foreach ([
            'src/misas/application/ver_misas_zona_data_build.php',
            'src/misas/application/crear_nuevo_periodo_data_build.php',
            'frontend/shared/layouts/MenusBurgerLayoutSupport.php',
            'frontend/shared/layouts/PillsLayout.php',
        ] as $fichero) {
            $this->assertDoesNotMatchRegularExpression('/json_encode\((?![^;]*JSON_HEX_TAG)[^;]*(columns_cuadricula|menuConfigArray|defaultGrupMenu)[^;]*;/', $this->codigo($fichero), $fichero);
        }
    }

    public function test_los_valores_de_la_peticion_dentro_de_un_script_van_codificados(): void
    {
        $sinCodificar = [
            'frontend/dbextern/view/ver_listas.phtml' => ['$region', '$dl', '$tipo_persona', '$new_id', '$id_nom_bdu'],
            'frontend/dbextern/view/ver_orbix.phtml' => ['$region', '$dl', '$tipo_persona', '$new_id'],
            'frontend/dbextern/view/sincro_index.phtml' => ['$region', '$dl_listas', '$tipo_persona'],
            'frontend/actividades/view/actividad_select_ubi.phtml' => ['addslashes($dl_org)'],
            'frontend/notas/view/form_notas_de_una_persona.phtml' => ['$f_acta_iso'],
            'frontend/actividadplazas/view/plazas_balance_que.phtml' => ['$Qid_tipo_activ'],
            'frontend/ubis/view/direccion_form.phtml' => ['$obj_dir'],
            'frontend/ubis/view/teleco_tabla.phtml' => ['$ficha'],
            'frontend/shared/view/tablaDB_formulario.phtml' => ['$clase_info'],
        ];
        foreach ($sinCodificar as $vista => $expresiones) {
            $codigo = $this->codigo($vista);
            foreach ($expresiones as $expr) {
                $this->assertDoesNotMatchRegularExpression('/<\?=\s*' . preg_quote($expr, '/') . '\s*;?\s*\?>/', $codigo, "$vista: $expr");
            }
        }
    }

    public function test_al_salir_se_borra_el_estado_de_las_listas_y_los_enlaces_no_se_reescriben_con_cadenas(): void
    {
        $codigo = $this->codigo('scripts/index.js.php');

        $this->assertGreaterThanOrEqual(2, substr_count($codigo, 'fnjs_olvidar_estado_de_listas()'), 'al salir y al caducar la sesión');
        $this->assertStringNotContainsString('"location.hash = \'#" + part[1]', $codigo);
        $this->assertStringNotContainsString('"fnjs_update_div(\'" + id_div + "\',\'" + url + "\')', $codigo);
    }
}
