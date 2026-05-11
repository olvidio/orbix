<?php

use src\actividadestudios\application\MatriculasListaData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $inicioIso = (string)($_POST['inicioIso'] ?? '');
    $finIso = (string)($_POST['finIso'] ?? '');
    if ($inicioIso === '' || $finIso === '') {
        throw new \InvalidArgumentException(_('Se requieren inicioIso y finIso'));
    }
    $data = MatriculasListaData::execute($inicioIso, $finIso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
