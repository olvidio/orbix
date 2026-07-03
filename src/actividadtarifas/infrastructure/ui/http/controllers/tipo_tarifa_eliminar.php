<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: elimina un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tarifa' => FuncTablasSupport::inputInt($_POST, 'id_tarifa'),
];

/** @var TipoTarifaEliminar $useCase */
$useCase = DependencyResolver::get(TipoTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
