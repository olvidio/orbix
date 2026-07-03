<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\application\UbisEditarNormalizeDlData;
use src\shared\domain\helpers\FuncTablasSupport;

$error = '';
$data = [];
try {
    $id_ubi = FuncTablasSupport::inputInt($_POST, 'id_ubi');
    $tipo_ubi = FuncTablasSupport::inputString($_POST, 'tipo_ubi');
    $nombre_ubi = FuncTablasSupport::inputString($_POST, 'nombre_ubi');
    $obj_pau = FuncTablasSupport::inputString($_POST, 'obj_pau');
    $data = ['obj_pau' => DependencyResolver::get(UbisEditarNormalizeDlData::class)->execute($id_ubi, $tipo_ubi, $nombre_ubi, $obj_pau)];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
