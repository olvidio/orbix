<?php

use src\actividadestudios\application\PlanEstudiosCaData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    if ($idActiv <= 0) {
        throw new \InvalidArgumentException(_('Se requiere id_activ'));
    }
    $data = PlanEstudiosCaData::execute($idActiv);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
