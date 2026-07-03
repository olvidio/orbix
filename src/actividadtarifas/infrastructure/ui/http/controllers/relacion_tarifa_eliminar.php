<?php


/**
 * Endpoint backend: elimina una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'),
];

/** @var RelacionTarifaEliminar $useCase */
$useCase = DependencyResolver::get(RelacionTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
