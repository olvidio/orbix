<?php

use src\shared\web\ContestarJson;
use src\notas\application\TesseraImprimirData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;


$error = '';
$data = [];

try {
    $id_nom = (int)FilterPostGet::post('id_nom');
    $data = (DependencyResolver::get(TesseraImprimirData::class))->execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
