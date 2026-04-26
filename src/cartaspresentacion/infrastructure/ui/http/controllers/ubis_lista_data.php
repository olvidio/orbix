<?php
/**
 * Endpoint backend: listado de centros con el estado de su carta de
 * presentacion, en dos variantes (delegacion del usuario o centros
 * extranjeros).
 */

use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use frontend\shared\web\ContestarJson;

$input = [
    'tipo_lista' => (string)filter_input(INPUT_POST, 'tipo_lista'),
    'poblacion_sel' => (string)filter_input(INPUT_POST, 'poblacion_sel'),
];
$data = CartasPresentacionUbisListaData::execute($input);
ContestarJson::enviar('', $data);
