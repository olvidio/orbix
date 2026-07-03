<?php


/**
 * Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_item'),
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tarifa'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_activ'),
];

/** @var RelacionTarifaUpdate $useCase */
$useCase = DependencyResolver::get(RelacionTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
