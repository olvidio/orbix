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
    'continuar' => (string)\src\shared\domain\helpers\FilterPostGet::post('continuar'),
    'status' => (int)\src\shared\domain\helpers\FilterPostGet::post('status'),
    'tipo_activ_sg' => (string)\src\shared\domain\helpers\FilterPostGet::post('tipo_activ_sg'),
    'id_ubi' => (int)\src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'dl_org' => (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'sel' => (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)\src\shared\domain\helpers\FilterPostGet::post('scroll_id'),
];
$stackGo = (int)\src\shared\domain\helpers\FilterPostGet::post('stack_go');

/** @var ListaActividadesSgListado $useCase */
$useCase = DependencyResolver::get(ListaActividadesSgListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
