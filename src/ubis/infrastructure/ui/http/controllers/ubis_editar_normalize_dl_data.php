<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\application\UbisEditarNormalizeDlData;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $id_ubi = input_int($_POST, 'id_ubi');
    $tipo_ubi = input_string($_POST, 'tipo_ubi');
    $nombre_ubi = input_string($_POST, 'nombre_ubi');
    $obj_pau = input_string($_POST, 'obj_pau');
    $data = ['obj_pau' => DependencyResolver::get(UbisEditarNormalizeDlData::class)->execute($id_ubi, $tipo_ubi, $nombre_ubi, $obj_pau)];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
