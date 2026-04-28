<?php

use src\actividadestudios\application\ActaNotasData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    $idAsignatura = (int)($_POST['id_asignatura'] ?? 0);
    if ($idActiv <= 0 || $idAsignatura <= 0) {
        throw new \InvalidArgumentException(_('Se requieren id_activ e id_asignatura'));
    }
    $data = ActaNotasData::execute($idActiv, $idAsignatura);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
