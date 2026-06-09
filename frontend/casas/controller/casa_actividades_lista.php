<?php
/**
 * Controlador AJAX HTML: listado de actividades por casa y periodo
 * (`tipo_lista=lista_activ`).
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = PostRequest::getDataFromUrl('/src/casas/casa_actividades_lista_data', $campos);
$payload = is_array($data) ? $data : [];

$oLista = new Lista();
$oLista->setGrupos($payload['a_grupos'] ?? []);
$oLista->setCabeceras($payload['a_cabeceras'] ?? []);
$oLista->setDatos($payload['a_valores'] ?? []);
echo $oLista->listaPaginada();
