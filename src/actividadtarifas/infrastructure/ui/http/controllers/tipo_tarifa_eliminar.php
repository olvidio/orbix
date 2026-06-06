<?php
/**
 * Endpoint backend: elimina un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_tarifa' => input_int($_POST, 'id_tarifa'),
];

/** @var TipoTarifaEliminar $useCase */
$useCase = DependencyResolver::get(TipoTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
