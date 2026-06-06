<?php

use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $op = input_string($_POST, 'op_genericas', '1') !== '0';
    /** @var AsignaturasConSeparadorOpcionesData $useCase */
    $useCase = DependencyResolver::get(AsignaturasConSeparadorOpcionesData::class);
    $data = $useCase->execute($op);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
