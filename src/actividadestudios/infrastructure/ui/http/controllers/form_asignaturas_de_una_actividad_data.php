<?php

use src\actividadestudios\application\FormAsignaturasDeUnaActividadData;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

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
    $dlError = RegionStgrAviso::esDlSinRegion($e) ? $e : $e->getPrevious();
    if ($dlError instanceof \Throwable && RegionStgrAviso::esDlSinRegion($dlError)) {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, $dlError);
        $aviso = RegionStgrAviso::formatear($problemas);
        $error = ($e !== $dlError) ? $e->getMessage() . '<br>' . $aviso : $aviso;
    } else {
        $error = $e->getMessage();
    }
}

ContestarJson::enviar($error, $data);
