<?php

use src\notas\application\ListadoAnualActasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $inicioIso = FuncTablasSupport::inputString($_POST, 'inicioIso');
    $finIso = FuncTablasSupport::inputString($_POST, 'finIso');
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
