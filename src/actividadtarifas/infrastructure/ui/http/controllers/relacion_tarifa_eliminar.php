<?php
/**
 * Endpoint backend: elimina una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_item' => input_int($_POST, 'id_item'),
];

/** @var RelacionTarifaEliminar $useCase */
$useCase = DependencyResolver::get(RelacionTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
