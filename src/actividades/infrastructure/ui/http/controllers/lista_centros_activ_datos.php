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
    'id_ctr_num' => (int)filter_input(INPUT_POST, 'id_ctr_num'),
    'id_ctr' => (array)filter_input(INPUT_POST, 'id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

/** @var ListaCentrosActivDatos $useCase */
$useCase = DependencyResolver::get(ListaCentrosActivDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
