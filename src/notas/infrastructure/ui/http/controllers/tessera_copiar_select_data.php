<?php

use src\notas\application\TesseraCopiarSelectData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $id_nom = (int)($_POST['id_nom'] ?? 0);
    if ($id_nom === 0) {
        throw new \RuntimeException(_("Se requiere id_nom"));
    }
    $data = TesseraCopiarSelectData::execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
