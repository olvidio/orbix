<?php
/**
 * Endpoint backend: actualiza plazas previstas de un ingreso (TablaEditable).
 */

use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = [
    'data' => input_string($_POST, 'data'),
    'colName' => input_string($_POST, 'colName'),
];

/** @var IngresoPlazasPrevistasUpdate $useCase */
$useCase = DependencyResolver::get(IngresoPlazasPrevistasUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
