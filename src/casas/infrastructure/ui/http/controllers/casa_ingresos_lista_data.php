<?php


/**
 * Endpoint backend: listado económico de actividades por casa
 * (`casa_ingresos_lista`).
 */

use src\casas\application\CasaIngresosListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_cdc' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasaIngresosListaData $useCase */
$useCase = DependencyResolver::get(CasaIngresosListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
