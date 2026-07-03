<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * JSON del listado para `lista_actividades_sg`: POST → {@see ListaActividadesSgListado}.
 * Sin `HashFront` ni HTML: celdas con `link_spec` opcional y, si aplica, `advertencia_demasiadas`;
 * el front firma y pinta en {@see frontend\actividades\controller\lista_actividades_sg}.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ListaActividadesSgListado;

$input = [
    'continuar' => (string)FilterPostGet::post('continuar'),
    'status' => (int)FilterPostGet::post('status'),
    'tipo_activ_sg' => (string)FilterPostGet::post('tipo_activ_sg'),
    'id_ubi' => (int)FilterPostGet::post('id_ubi'),
    'periodo' => (string)FilterPostGet::post('periodo'),
    'year' => (string)FilterPostGet::post('year'),
    'dl_org' => (string)FilterPostGet::post('dl_org'),
    'empiezamin' => (string)FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)FilterPostGet::post('empiezamax'),
    'sel' => (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)FilterPostGet::post('scroll_id'),
];
$stackGo = (int)FilterPostGet::post('stack_go');

/** @var ListaActividadesSgListado $useCase */
$useCase = DependencyResolver::get(ListaActividadesSgListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
