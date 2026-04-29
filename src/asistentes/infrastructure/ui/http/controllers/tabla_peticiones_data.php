<?php

use src\asistentes\application\TablaPeticionesData;
use frontend\shared\web\ContestarJson;
use frontend\shared\web\Lista;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

$data = TablaPeticionesData::build($_POST);

if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}

$apiSaveUrl = rtrim(AppUrlConfig::getApiBaseUrl(), '/') . '/' . ltrim((string)(($data['paths']['asistente_guardar'] ?? '')), '/');

/**
 resolves `peticiones_parts` en HTML para Lista.
 * @param array<string, mixed>|string $col2
 */
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
        $t = (string)($p['t'] ?? '');
        if ($t === 'p') {
            $out .= (string)($p['s'] ?? '');
        } elseif ($t === 'm') {
            $h = $p['h'] ?? [];
            if (!is_array($h)) {
                continue;
            }
            $oHash = new HashFront();
            $oHash->setUrl($apiSaveUrl);
            $oHash->setArrayCamposHidden($h);
            $param = $oHash->getParamAjax();
            $s = (string)($p['s'] ?? '');
            $out .= '<span class="link" onClick="fnjs_cambiar_actividad(\'' . $param . '\')">'
                . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . '</span>';
        }
    }

    return $out;
};

$a_valores = (array)($data['a_valores'] ?? []);
foreach ($a_valores as $k => $fila) {
    if (!is_array($fila) || !is_int($k)) {
        continue;
    }
    if (array_key_exists(2, $fila) && is_array($fila[2])) {
        $a_valores[$k][2] = $resolveCell($fila[2]);
    }
}
$data['a_valores'] = $a_valores;

$oTabla = new Lista();
$oTabla->setId_tabla('tabla_peticiones');
$oTabla->setCabeceras((array)($data['a_cabeceras'] ?? []));
$oTabla->setBotones((array)($data['a_botones'] ?? []));
$oTabla->setDatos($a_valores);

$data['tabla_html'] = $oTabla->mostrar_tabla_html();

$url_guardar_ajax = $apiSaveUrl;
$data['url_guardar_ajax'] = $url_guardar_ajax;
$data['url_guardar_ajax_json'] = json_encode($url_guardar_ajax, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

unset($data['a_cabeceras'], $data['a_botones'], $data['a_valores'], $data['paths']);

ContestarJson::enviar('', $data);
