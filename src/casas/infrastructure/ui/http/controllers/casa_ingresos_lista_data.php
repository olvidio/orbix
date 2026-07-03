<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: listado económico de actividades por casa
 * (`casa_ingresos_lista`).
 */

use src\casas\application\CasaIngresosListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_cdc' => FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasaIngresosListaData $useCase */
$useCase = DependencyResolver::get(CasaIngresosListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
