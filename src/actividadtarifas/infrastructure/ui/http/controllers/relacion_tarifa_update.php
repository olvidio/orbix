<?php
/**
 * Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_item' => input_string($_POST, 'id_item'),
    'id_tarifa' => input_int($_POST, 'id_tarifa'),
    'id_tipo_activ' => input_int($_POST, 'id_tipo_activ'),
];

/** @var RelacionTarifaUpdate $useCase */
$useCase = DependencyResolver::get(RelacionTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
