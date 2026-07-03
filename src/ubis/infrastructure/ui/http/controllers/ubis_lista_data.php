<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisListaData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qnombre_ubi = FuncTablasSupport::inputString($_POST, 'nombre_ubi');

/** @var UbisListaData $useCase */
$useCase = DependencyResolver::get(UbisListaData::class);
$data = $useCase->execute($Qnombre_ubi);
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(FuncTablasSupport::inputString($data, 'error'), []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
]);
