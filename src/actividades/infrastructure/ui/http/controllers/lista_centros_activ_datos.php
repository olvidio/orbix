<?php
/**
 * Endpoint backend para `lista_centros_activ`.
 * Recibe los filtros via POST y delega en ListaCentrosActivDatos.
 * Responde con JSON mediante ContestarJson::enviar (patron refactor.md).
 */

use src\actividades\application\ListaCentrosActivDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ctr_num' => (int)filter_post('id_ctr_num'),
    'id_ctr' => (array)filter_post('id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
];

/** @var ListaCentrosActivDatos $useCase */
$useCase = DependencyResolver::get(ListaCentrosActivDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
