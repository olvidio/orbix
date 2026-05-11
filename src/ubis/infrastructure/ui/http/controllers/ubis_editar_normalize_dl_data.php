<?php

use src\shared\web\ContestarJson;
use src\ubis\application\UbisEditarNormalizeDlData;

$error = '';
$data = [];
try {
    $id_ubi = (int)($_POST['id_ubi'] ?? 0);
    $tipo_ubi = (string)($_POST['tipo_ubi'] ?? '');
    $nombre_ubi = (string)($_POST['nombre_ubi'] ?? '');
    $obj_pau = (string)($_POST['obj_pau'] ?? '');
    $data = ['obj_pau' => UbisEditarNormalizeDlData::execute($id_ubi, $tipo_ubi, $nombre_ubi, $obj_pau)];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
