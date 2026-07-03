<?php

use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $op = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'op_genericas', '1') !== '0';
    /** @var AsignaturasConSeparadorOpcionesData $useCase */
    $useCase = DependencyResolver::get(AsignaturasConSeparadorOpcionesData::class);
    $data = $useCase->execute($op);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
