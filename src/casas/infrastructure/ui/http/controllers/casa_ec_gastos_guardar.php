<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: guardar gastos/aportaciones mensuales (`casa_ec_gastos_guardar`).
 */

use src\casas\application\CasaEcGastosGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = array_merge($_POST, [
    'id_ubi' => FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'year' => FuncTablasSupport::inputInt($_POST, 'year'),
]);
for ($m = 1; $m < 13; $m++) {
    $input["g$m"] = FuncTablasSupport::inputString($_POST, "g$m");
    $input["ap_sv$m"] = FuncTablasSupport::inputString($_POST, "ap_sv$m");
    $input["ap_sf$m"] = FuncTablasSupport::inputString($_POST, "ap_sf$m");
}

/** @var CasaEcGastosGuardar $useCase */
$useCase = DependencyResolver::get(CasaEcGastosGuardar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data']);
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
