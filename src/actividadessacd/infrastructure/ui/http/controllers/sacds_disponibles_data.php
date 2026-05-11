<?php
/**
 * Endpoint backend: devuelve los sacd candidatos para asignar a una
 * actividad (sacd del centro encargado + sacd globales segun bitmask
 * `seleccion`).
 */

use src\actividadessacd\application\SacdsDisponiblesData;
use src\shared\web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

$data = SacdsDisponiblesData::execute($input);
ContestarJson::enviar('', $data);
