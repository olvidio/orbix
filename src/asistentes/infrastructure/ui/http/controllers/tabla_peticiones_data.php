<?php

use Psr\Container\ContainerInterface;
/**
 * JSON para {@see \src\asistentes\application\TablaPeticionesData}.
 * Tabla HTML, firmas y URL AJAX: {@see \frontend\asistentes\helpers\TablaPeticionesRender}.
 */

use src\asistentes\application\TablaPeticionesData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\TablaPeticionesData $useCase */
$useCase = $container->get(TablaPeticionesData::class);
$data = $useCase->build($_POST);

if (isset($data['error'])) {
    $error = is_string($data['error']) ? $data['error'] : '';
    ContestarJson::enviar($error, 'none');
    return;
}

ContestarJson::enviar('', $data);
