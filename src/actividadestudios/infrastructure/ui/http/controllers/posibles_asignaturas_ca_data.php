<?php

use src\actividadestudios\application\PosiblesAsignaturasCaData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    $nomActiv = (string)($_POST['nom_activ'] ?? '');
    if ($idActiv <= 0) {
        throw new \InvalidArgumentException(_('Se requiere id_activ'));
    }
    $data = PosiblesAsignaturasCaData::execute($idActiv, $nomActiv);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
