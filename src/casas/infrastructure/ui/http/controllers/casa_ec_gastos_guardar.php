<?php
/**
 * Endpoint backend: guardar gastos/aportaciones mensuales (`casa_ec_gastos_guardar`).
 */

use src\casas\application\CasaEcGastosGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = array_merge($_POST, [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'year' => input_int($_POST, 'year'),
]);
for ($m = 1; $m < 13; $m++) {
    $input["g$m"] = input_string($_POST, "g$m");
    $input["ap_sv$m"] = input_string($_POST, "ap_sv$m");
    $input["ap_sf$m"] = input_string($_POST, "ap_sf$m");
}

/** @var CasaEcGastosGuardar $useCase */
$useCase = DependencyResolver::get(CasaEcGastosGuardar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data']);
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
