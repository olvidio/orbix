<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosSListaData;
use src\shared\web\ContestarJson;

/** @var CentrosSListaData $useCase */
$useCase = DependencyResolver::get(CentrosSListaData::class);
$data = $useCase->execute();
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(\src\shared\domain\helpers\FuncTablasSupport::inputString($data, 'error'), []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
    'num_total_s' => $data['num_total_s'],
]);
