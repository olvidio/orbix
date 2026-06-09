<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\web\Lista;

/**
 * Completa el JSON de {@see \src\asistentes\application\TablaPeticionesData} para la vista.
 */
final class TablaPeticionesRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $tabla = asistentes_tabla_peticiones_from_payload($payload);
        $a_valores = $tabla['valores'];
        foreach ($a_valores as $k => $fila) {
            if (!is_int($k) || !is_array($fila)) {
                continue;
            }
            $a_valores[$k] = asistentes_tabla_peticiones_resolve_cell($fila, $tabla['api_save_url']);
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('tabla_peticiones');
        $oTabla->setCabeceras($tabla['cabeceras']);
        $oTabla->setBotones($tabla['botones']);
        $oTabla->setDatos($a_valores);

        $payload['tabla_html'] = $oTabla->mostrar_tabla_html();
        $payload['url_guardar_ajax'] = $tabla['api_save_url'];
        $payload['url_guardar_ajax_json'] = json_encode($tabla['api_save_url'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        unset($payload['a_cabeceras'], $payload['a_botones'], $payload['a_valores'], $payload['paths']);

        return $payload;
    }
}
