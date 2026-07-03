<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisTablaData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$input = $_POST;

/** @var UbisTablaData $useCase */
$useCase = DependencyResolver::get(UbisTablaData::class);
$data = $useCase->execute($input);
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(FuncTablasSupport::inputString($data, 'error'), []);
    return;
}
ContestarJson::enviar('', $data);
