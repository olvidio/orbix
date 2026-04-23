<?php
/**
 * Endpoint backend: crea o actualiza un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaUpdate;
use web\ContestarJson;

$input = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
    'id_ubi_padre' => (int)filter_input(INPUT_POST, 'id_ubi_padre'),
    'id_ubi_hijo' => (int)filter_input(INPUT_POST, 'id_ubi_hijo'),
];

$error = GrupoCasaUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
