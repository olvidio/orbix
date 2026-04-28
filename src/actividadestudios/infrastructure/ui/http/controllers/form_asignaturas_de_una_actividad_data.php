<?php

use src\actividadestudios\application\FormAsignaturasDeUnaActividadData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $pau = (string)($_POST['pau'] ?? '');
    $idPau = (int)($_POST['id_pau'] ?? 0);
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    $idAsignatura = (int)($_POST['id_asignatura'] ?? 0);
    $sel = isset($_POST['sel']) && is_array($_POST['sel']) ? $_POST['sel'] : null;
    $data = FormAsignaturasDeUnaActividadData::execute($pau, $idPau, $idActiv, $idAsignatura, $sel);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
