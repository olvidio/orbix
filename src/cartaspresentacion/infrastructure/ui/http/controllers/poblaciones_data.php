<?php
/**
 * Endpoint backend: opciones del desplegable de poblaciones segun el
 * filtro elegido (`get_H`, `get_r`, `get_dl`).
 *
 * El payload sigue el contrato estandar de desplegables dinamicos
 * (ver `refactor.md`), de modo que el frontend lo transforma con el
 * helper JS `fnjs_construir_desplegable`.
 */

use src\cartaspresentacion\application\CartasPresentacionPoblacionesData;
use src\shared\web\ContestarJson;

$input = ['filtro' => (string)filter_input(INPUT_POST, 'filtro')];
$data = CartasPresentacionPoblacionesData::execute($input);
ContestarJson::enviar('', $data);
