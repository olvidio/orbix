<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisTablaData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = $_POST;

/** @var UbisTablaData $useCase */
$useCase = DependencyResolver::get(UbisTablaData::class);
$data = $useCase->execute($input);
if (array_key_exists('error', $data)) {
    ContestarJson::enviar(input_string($data, 'error'), []);
    return;
}
ContestarJson::enviar('', $data);
