<?php
/**
 * Endpoint backend: opciones del formulario de busqueda de cartas de
 * presentacion (region, pais, delegacion).
 */

use src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData;
use frontend\shared\web\ContestarJson;

$data = CartasPresentacionBuscarOpcionesData::execute();
ContestarJson::enviar('', $data);
