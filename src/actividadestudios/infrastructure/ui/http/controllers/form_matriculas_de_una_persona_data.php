<?php

use src\actividadestudios\application\FormMatriculasDeUnaPersonaData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idNom = (int)($_POST['id_pau'] ?? 0);
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    $idAsignatura = (int)($_POST['id_asignatura'] ?? 0);
    $sel = isset($_POST['sel']) && is_array($_POST['sel']) ? $_POST['sel'] : null;
    if ($idNom <= 0 || $idActiv <= 0) {
        throw new \InvalidArgumentException(_('Se requieren id_pau e id_activ'));
    }
    $data = FormMatriculasDeUnaPersonaData::execute($idNom, $idActiv, $idAsignatura, $sel);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
