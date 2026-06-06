<?php
/**
 * Endpoint backend: crea o actualiza un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_tarifa' => input_string($_POST, 'id_tarifa'),
    'letra' => input_string($_POST, 'letra'),
    'modo' => input_string($_POST, 'modo'),
    'observ' => input_string($_POST, 'observ'),
];

/** @var TipoTarifaUpdate $useCase */
$useCase = DependencyResolver::get(TipoTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
