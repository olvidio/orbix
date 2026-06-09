<?php

use src\notas\application\TesseraVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


$error = '';
$data = [];

try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = (DependencyResolver::get(TesseraVerData::class))->execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
