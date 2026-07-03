<?php


/**
 * Endpoint backend: crear/actualizar ingreso y tarifa de actividad.
 */

use src\casas\application\CasaIngresoUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tarifa'),
    'precio' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'precio'),
    'ingresos' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ingresos'),
    'num_asistentes' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'num_asistentes'),
    'observ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var CasaIngresoUpdate $useCase */
$useCase = DependencyResolver::get(CasaIngresoUpdate::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data']);
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
