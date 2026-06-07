<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisListaData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$Qnombre_ubi = input_string($_POST, 'nombre_ubi');

/** @var UbisListaData $useCase */
$useCase = DependencyResolver::get(UbisListaData::class);
$data = $useCase->execute($Qnombre_ubi);
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(input_string($data, 'error'), []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
]);
