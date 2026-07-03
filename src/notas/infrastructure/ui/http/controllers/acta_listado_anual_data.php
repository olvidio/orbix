<?php

use src\notas\application\ListadoAnualActasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $inicioIso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicioIso');
    $finIso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'finIso');
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
