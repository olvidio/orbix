<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use function frontend\shared\helpers\payload_string;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
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
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $apiSaveUrl = rtrim(AppUrlConfig::getApiBaseUrl(), '/') . '/' . ltrim(payload_string($paths, 'asistente_guardar'), '/');

        $resolveCell = static function ($col2) use ($apiSaveUrl): string {
            if (is_string($col2)) {
                return $col2;
            }
            if (!is_array($col2)) {
                return '';
            }
            $parts = $col2['peticiones_parts'] ?? [];
            if (!is_array($parts)) {
                return '';
            }
            $out = '';
            foreach ($parts as $p) {
                if (!is_array($p)) {
                    continue;
                }
                $t = payload_string($p, 't');
                if ($t === 'p') {
                    $out .= payload_string($p, 's');
                } elseif ($t === 'm') {
                    $h = $p['h'] ?? [];
                    if (!is_array($h)) {
                        continue;
                    }
                    $oHash = new HashFront();
                    $oHash->setUrl($apiSaveUrl);
                    $oHash->setArrayCamposHidden($h);
                    $param = $oHash->getParamAjax();
                    $s = payload_string($p, 's');
                    $out .= '<span class="link" onClick="fnjs_cambiar_actividad(\'' . $param . '\')">'
                        . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . '</span>';
                }
            }

            return $out;
        };

        $a_valores = (array)($payload['a_valores'] ?? []);
        foreach ($a_valores as $k => $fila) {
            if (!is_array($fila) || !is_int($k)) {
                continue;
            }
            if (array_key_exists(2, $fila) && is_array($fila[2])) {
                $a_valores[$k][2] = $resolveCell($fila[2]);
            }
        }
        $payload['a_valores'] = $a_valores;

        $oTabla = new Lista();
        $oTabla->setId_tabla('tabla_peticiones');
        $oTabla->setCabeceras((array)($payload['a_cabeceras'] ?? []));
        $oTabla->setBotones((array)($payload['a_botones'] ?? []));
        $oTabla->setDatos($a_valores);

        $payload['tabla_html'] = $oTabla->mostrar_tabla_html();
        $payload['url_guardar_ajax'] = $apiSaveUrl;
        $payload['url_guardar_ajax_json'] = json_encode($apiSaveUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        unset($payload['a_cabeceras'], $payload['a_botones'], $payload['a_valores'], $payload['paths']);

        return $payload;
    }
}
