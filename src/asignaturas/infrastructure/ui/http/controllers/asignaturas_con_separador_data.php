<?php

use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $op = FuncTablasSupport::inputString($_POST, 'op_genericas', '1') !== '0';
    /** @var AsignaturasConSeparadorOpcionesData $useCase */
    $useCase = DependencyResolver::get(AsignaturasConSeparadorOpcionesData::class);
    $data = $useCase->execute($op);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
