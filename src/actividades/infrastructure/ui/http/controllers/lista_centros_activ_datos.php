<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend para `lista_centros_activ`.
 * Recibe los filtros via POST y delega en ListaCentrosActivDatos.
 * Responde con JSON mediante ContestarJson::enviar (patron refactor.md).
 */

use src\actividades\application\ListaCentrosActivDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ctr_num' => (int)\src\shared\domain\helpers\FilterPostGet::post('id_ctr_num'),
    'id_ctr' => (array)\src\shared\domain\helpers\FilterPostGet::post('id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
];

/** @var ListaCentrosActivDatos $useCase */
$useCase = DependencyResolver::get(ListaCentrosActivDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
