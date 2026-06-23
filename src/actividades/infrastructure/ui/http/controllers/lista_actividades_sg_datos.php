<?php
/**
 * JSON del listado para `lista_actividades_sg`: POST → {@see ListaActividadesSgListado}.
 * Sin `HashFront` ni HTML: celdas con `link_spec` opcional y, si aplica, `advertencia_demasiadas`;
 * el front firma y pinta en {@see frontend\actividades\controller\lista_actividades_sg}.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ListaActividadesSgListado;

$input = [
    'continuar' => (string)filter_post('continuar'),
    'status' => (int)filter_post('status'),
    'tipo_activ_sg' => (string)filter_post('tipo_activ_sg'),
    'id_ubi' => (int)filter_post('id_ubi'),
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'dl_org' => (string)filter_post('dl_org'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
    'sel' => (array)filter_post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)filter_post('scroll_id'),
];
$stackGo = (int)filter_post('stack_go');

/** @var ListaActividadesSgListado $useCase */
$useCase = DependencyResolver::get(ListaActividadesSgListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
