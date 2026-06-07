<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => filter_input(INPUT_POST, 'tipo_plantilla'),
    'periodo' => filter_input(INPUT_POST, 'periodo'),
    'orden' => filter_input(INPUT_POST, 'orden'),
    'empiezamin' => filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => filter_input(INPUT_POST, 'empiezamax'),
    'fila' => filter_input(INPUT_POST, 'fila'),
    'columna' => filter_input(INPUT_POST, 'columna'),
    'seleccion' => filter_input(INPUT_POST, 'seleccion'),
];

/** @var CuadriculaZonaGridData $useCase */
$useCase = DependencyResolver::get(CuadriculaZonaGridData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
