<?php
/**
 * Fragmento HTML con el calendario de actividades de casas / oficinas en un
 * periodo dado. Se invoca via AJAX y el resultado se inyecta en el DOM.
 *
 * Toda la logica vive en `src\actividades\application\CalendarioListasDatos`
 * y se consume via PostRequest. Este controlador solo parsea el POST y echoea
 * el HTML resultante.
 *
 * Migrado desde frontend/actividades/controller/calendario_listas.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividades_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qver_ctr = (string)filter_input(INPUT_POST, 'ver_ctr');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qyeardefault = (string)filter_input(INPUT_POST, 'yeardefault');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qaid_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$data = PostRequest::getDataFromUrl('/src/actividades/calendario_listas_datos', [
    'que' => $Qque,
    'ver_ctr' => $Qver_ctr,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'yeardefault' => $Qyeardefault,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'id_cdc' => $Qaid_cdc,
]);

echo tessera_imprimir_string($data['html'] ?? '');
