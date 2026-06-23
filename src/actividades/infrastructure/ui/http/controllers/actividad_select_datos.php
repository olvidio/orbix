<?php
/**
 * JSON del listado para `actividad_select`: filtros POST → {@see ActividadSelectListado}.
 * Sin `HashFront` ni HTML: celdas con `link_spec` y, si aplica, `advertencia_demasiadas`
 * con `*_link_spec`; el front firma y pinta en {@see frontend\actividades\controller\actividad_select}.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ActividadSelectListado;

$input = [
    'continuar' => (string)filter_post('continuar'),
    'modo' => (string)filter_post('modo'),
    'status' => (int)filter_post('status'),
    'id_tipo_activ' => (string)filter_post('id_tipo_activ'),
    'filtro_lugar' => (string)filter_post('filtro_lugar'),
    'id_ubi' => (int)filter_post('id_ubi'),
    'nom_activ' => (string)filter_post('nom_activ'),
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'dl_org' => (string)filter_post('dl_org'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
    'fases_on' => (array)filter_post('fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'fases_off' => (array)filter_post('fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'publicado' => (int)filter_post('publicado'),
    'ssfsv' => (string)filter_post('ssfsv'),
    'sasistentes' => (string)filter_post('sasistentes'),
    'sactividad' => (string)filter_post('sactividad'),
    'sactividad2' => (string)filter_post('sactividad2'),
    'sel' => (array)filter_post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'scroll_id' => (string)filter_post('scroll_id'),
];
$stackGo = (int)filter_post('stack_go');

/** @var ActividadSelectListado $useCase */
$useCase = DependencyResolver::get(ActividadSelectListado::class);
$data = $useCase->ejecutar($input, $stackGo);

ContestarJson::enviar('', $data);
