<?php
/**
 * Endpoint backend que calcula el listado de actividades para la pantalla
 * `actividad_select`. Recibe los filtros via POST y delega en el caso de
 * uso ActividadSelectListado. La respuesta se sirve como JSON mediante
 * ContestarJson::enviar (patron establecido en refactor.md).
 */

use src\actividades\application\ActividadSelectListado;
use web\ContestarJson;

$input = [
    'continuar' => (string)filter_input(INPUT_POST, 'continuar'),
    'modo' => (string)filter_input(INPUT_POST, 'modo'),
    'status' => (int)filter_input(INPUT_POST, 'status'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'filtro_lugar' => (string)filter_input(INPUT_POST, 'filtro_lugar'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'nom_activ' => (string)filter_input(INPUT_POST, 'nom_activ'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'fases_on' => (array)filter_input(INPUT_POST, 'fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'fases_off' => (array)filter_input(INPUT_POST, 'fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'publicado' => (int)filter_input(INPUT_POST, 'publicado'),
    'ssfsv' => (string)filter_input(INPUT_POST, 'ssfsv'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'sactividad2' => (string)filter_input(INPUT_POST, 'sactividad2'),
    'sel' => (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)filter_input(INPUT_POST, 'scroll_id'),
];
$stackGo = (int)filter_input(INPUT_POST, 'stack_go');

$useCase = new ActividadSelectListado();
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
