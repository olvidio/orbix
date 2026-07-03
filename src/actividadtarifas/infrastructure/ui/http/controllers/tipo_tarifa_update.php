<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: crea o actualiza un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tarifa' => FuncTablasSupport::inputString($_POST, 'id_tarifa'),
    'letra' => FuncTablasSupport::inputString($_POST, 'letra'),
    'modo' => FuncTablasSupport::inputString($_POST, 'modo'),
    'observ' => FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var TipoTarifaUpdate $useCase */
$useCase = DependencyResolver::get(TipoTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
