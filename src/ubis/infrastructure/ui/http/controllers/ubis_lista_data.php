<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisListaData;
use src\shared\web\ContestarJson;

$Qnombre_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'nombre_ubi');

/** @var UbisListaData $useCase */
$useCase = DependencyResolver::get(UbisListaData::class);
$data = $useCase->execute($Qnombre_ubi);
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(\src\shared\domain\helpers\FuncTablasSupport::inputString($data, 'error'), []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
]);
