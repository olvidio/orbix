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
    'continuar' => (string)FilterPostGet::post('continuar'),
    'modo' => (string)FilterPostGet::post('modo'),
    'status' => (int)FilterPostGet::post('status'),
    'id_tipo_activ' => (string)FilterPostGet::post('id_tipo_activ'),
    'filtro_lugar' => (string)FilterPostGet::post('filtro_lugar'),
    'id_ubi' => (int)FilterPostGet::post('id_ubi'),
    'nom_activ' => (string)FilterPostGet::post('nom_activ'),
    'periodo' => (string)FilterPostGet::post('periodo'),
    'year' => (string)FilterPostGet::post('year'),
    'dl_org' => (string)FilterPostGet::post('dl_org'),
    'empiezamin' => (string)FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)FilterPostGet::post('empiezamax'),
    'fases_on' => (array)FilterPostGet::post('fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'fases_off' => (array)FilterPostGet::post('fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'publicado' => (int)FilterPostGet::post('publicado'),
    'ssfsv' => (string)FilterPostGet::post('ssfsv'),
    'sasistentes' => (string)FilterPostGet::post('sasistentes'),
    'sactividad' => (string)FilterPostGet::post('sactividad'),
    'sactividad2' => (string)FilterPostGet::post('sactividad2'),
    'sel' => (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)FilterPostGet::post('scroll_id'),
];
$stackGo = (int)FilterPostGet::post('stack_go');

/** @var ActividadSelectListado $useCase */
$useCase = DependencyResolver::get(ActividadSelectListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
