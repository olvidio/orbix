<?php
/**
 * JSON del listado para `lista_actividades_sg`: POST → {@see ListaActividadesSgListado}.
 * Sin `HashFront` ni HTML: celdas con `link_spec` opcional y, si aplica, `advertencia_demasiadas`;
 * el front firma y pinta en {@see frontend\actividades\controller\lista_actividades_sg}.
 */

use src\shared\web\ContestarJson;
use src\actividades\application\ListaActividadesSgListado;

$input = [
    'continuar' => (string)filter_input(INPUT_POST, 'continuar'),
    'status' => (int)filter_input(INPUT_POST, 'status'),
    'tipo_activ_sg' => (string)filter_input(INPUT_POST, 'tipo_activ_sg'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'sel' => (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)filter_input(INPUT_POST, 'scroll_id'),
];
$stackGo = (int)filter_input(INPUT_POST, 'stack_go');

$useCase = new ListaActividadesSgListado();
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
