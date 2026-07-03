<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * JSON del listado para `actividad_select`: filtros POST → {@see ActividadSelectListado}.
 * Sin `HashFront` ni HTML: celdas con `link_spec` y, si aplica, `advertencia_demasiadas`
 * con `*_link_spec`; el front firma y pinta en {@see frontend\actividades\controller\actividad_select}.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ActividadSelectListado;

$input = [
    'continuar' => (string)\src\shared\domain\helpers\FilterPostGet::post('continuar'),
    'modo' => (string)\src\shared\domain\helpers\FilterPostGet::post('modo'),
    'status' => (int)\src\shared\domain\helpers\FilterPostGet::post('status'),
    'id_tipo_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ'),
    'filtro_lugar' => (string)\src\shared\domain\helpers\FilterPostGet::post('filtro_lugar'),
    'id_ubi' => (int)\src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'nom_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('nom_activ'),
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'dl_org' => (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'fases_on' => (array)\src\shared\domain\helpers\FilterPostGet::post('fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'fases_off' => (array)\src\shared\domain\helpers\FilterPostGet::post('fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'publicado' => (int)\src\shared\domain\helpers\FilterPostGet::post('publicado'),
    'ssfsv' => (string)\src\shared\domain\helpers\FilterPostGet::post('ssfsv'),
    'sasistentes' => (string)\src\shared\domain\helpers\FilterPostGet::post('sasistentes'),
    'sactividad' => (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad'),
    'sactividad2' => (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad2'),
    'sel' => (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)\src\shared\domain\helpers\FilterPostGet::post('scroll_id'),
];
$stackGo = (int)\src\shared\domain\helpers\FilterPostGet::post('stack_go');

/** @var ActividadSelectListado $useCase */
$useCase = DependencyResolver::get(ActividadSelectListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
