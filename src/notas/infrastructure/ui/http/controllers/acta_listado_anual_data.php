<?php

use src\notas\application\ListadoAnualActasData;
use web\ContestarJson;

$error = '';
$data = [];
try {
    $inicioIso = (string)($_POST['inicioIso'] ?? '');
    $finIso = (string)($_POST['finIso'] ?? '');
    if (empty($inicioIso) || empty($finIso)) {
        throw new \RuntimeException(_("Se requieren inicioIso y finIso en formato Y-m-d"));
    }
    $data = ['aActas' => ListadoAnualActasData::execute($inicioIso, $finIso)];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
