<?php
/**
 * JSON para {@see \src\asistentes\application\TablaPeticionesData}.
 * Tabla HTML, firmas y URL AJAX: {@see \frontend\asistentes\helpers\TablaPeticionesRender}.
 */

use src\asistentes\application\TablaPeticionesData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = $GLOBALS['container']->get(TablaPeticionesData::class)->build($_POST);

if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}

ContestarJson::enviar('', $data);
