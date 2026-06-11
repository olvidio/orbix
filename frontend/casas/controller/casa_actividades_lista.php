<?php
/**
 * Controlador AJAX HTML: listado de actividades por casa y periodo
 * (`tipo_lista=lista_activ`).
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$campos = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = casas_post_data(PostRequest::getDataFromUrl('/src/casas/casa_actividades_lista_data', $campos));
$lista = casas_actividades_lista_from_payload($data);

$oLista = new Lista();
$oLista->setGrupos($lista['grupos']);
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
ajax_json_html($oLista->listaPaginada());
