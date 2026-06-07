<?php

use src\notas\application\ListadoAnualActasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $inicioIso = input_string($_POST, 'inicioIso');
    $finIso = input_string($_POST, 'finIso');
    if (empty($inicioIso) || empty($finIso)) {
        throw new \RuntimeException(_("Se requieren inicioIso y finIso en formato Y-m-d"));
    }
    $data = [
        'aActas' => (DependencyResolver::get(ListadoAnualActasData::class))->execute($inicioIso, $finIso),
        'inicio_local' => (new DateTimeLocal($inicioIso))->getFromLocal(),
        'fin_local' => (new DateTimeLocal($finIso))->getFromLocal(),
    ];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
