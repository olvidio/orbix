<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\support\MisasBuildInput;
use src\shared\web\ContestarJson;

$in = [
    'id_zona' => filter_post('id_zona'),
    'tipo_plantilla' => filter_post('tipo_plantilla'),
    'periodo' => filter_post('periodo'),
    'orden' => filter_post('orden'),
    'empiezamin' => filter_post('empiezamin'),
    'empiezamax' => filter_post('empiezamax'),
    'fila' => filter_post('fila'),
    'columna' => filter_post('columna'),
    'seleccion' => filter_post('seleccion'),
];

/** @var CuadriculaZonaGridData $useCase */
$useCase = DependencyResolver::get(CuadriculaZonaGridData::class);
$result = $useCase->build($in);

$error = MisasBuildInput::string($result, 'error');
unset($result['error']);

ContestarJson::enviar($error, $result);
