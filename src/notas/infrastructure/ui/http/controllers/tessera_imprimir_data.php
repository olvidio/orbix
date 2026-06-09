<?php

use src\shared\web\ContestarJson;
use src\notas\application\TesseraImprimirData;
use src\shared\infrastructure\DependencyResolver;


$error = '';
$data = [];

try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = (DependencyResolver::get(TesseraImprimirData::class))->execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
