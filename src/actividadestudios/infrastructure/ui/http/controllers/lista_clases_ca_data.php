<?php

use src\actividadestudios\application\ListaClasesCaData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    if ($idActiv <= 0) {
        throw new \InvalidArgumentException(_('Se requiere id_activ'));
    }
    $data = ListaClasesCaData::execute($idActiv);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
