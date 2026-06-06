<?php
/**
 * Endpoint backend: crear/actualizar ingreso y tarifa de actividad.
 */

use src\casas\application\CasaIngresoUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'id_tarifa' => input_string($_POST, 'id_tarifa'),
    'precio' => input_string($_POST, 'precio'),
    'ingresos' => input_string($_POST, 'ingresos'),
    'num_asistentes' => input_int($_POST, 'num_asistentes'),
    'observ' => input_string($_POST, 'observ'),
];

/** @var CasaIngresoUpdate $useCase */
$useCase = DependencyResolver::get(CasaIngresoUpdate::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data']);
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
