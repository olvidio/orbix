<?php


/**
 * Endpoint backend: actualiza plazas previstas de un ingreso (TablaEditable).
 */

use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'data' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'data'),
    'colName' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'colName'),
];

/** @var IngresoPlazasPrevistasUpdate $useCase */
$useCase = DependencyResolver::get(IngresoPlazasPrevistasUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
