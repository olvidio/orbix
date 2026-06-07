<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerMisasZonaData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_input(INPUT_POST, 'id_zona'),
    'empiezamin' => filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => filter_input(INPUT_POST, 'empiezamax'),
    'seleccion' => filter_input(INPUT_POST, 'seleccion'),
];

/** @var VerMisasZonaData $useCase */
$useCase = DependencyResolver::get(VerMisasZonaData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
