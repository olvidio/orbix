<?php
/**
 * Endpoint backend: elimina un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaEliminar;
use web\ContestarJson;

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
];

$error = GrupoCasaEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
