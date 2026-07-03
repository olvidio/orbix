<?php

use src\notas\application\TesseraVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;


$error = '';
$data = [];

try {
    $id_nom = (int)FilterPostGet::post('id_nom');
    $data = (DependencyResolver::get(TesseraVerData::class))->execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
