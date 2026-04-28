<?php

use src\actividadestudios\application\E43CertificadoData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $idNom = (int)($_POST['id_nom'] ?? 0);
    $idActiv = (int)($_POST['id_activ'] ?? 0);
    if ($idNom <= 0 || $idActiv <= 0) {
        throw new \InvalidArgumentException(_('Se requieren id_nom e id_activ'));
    }
    $data = E43CertificadoData::execute($idNom, $idActiv, true);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
