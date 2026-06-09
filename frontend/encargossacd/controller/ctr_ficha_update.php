<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy frontend a `/src/encargossacd/ctr_ficha_update`
 * ({@see \src\encargossacd\application\CtrFichaUpdate}).
 *
 * El JS consumidor (`ctr_ficha.phtml`) espera texto plano: muestra `alert(rta_txt)`
 * cuando el cuerpo no esta vacio y refresca la ficha en `.done()`. Por eso el proxy
 * reenvia el POST al endpoint JSON y, si PostRequest detecta `success=false`,
 * hace `exit` emitiendo el texto del mensaje; en exito devolvemos cuerpo vacio.
 *
 * Cuando todos los consumidores migren a JSON estricto, este proxy puede eliminarse
 * y el JS llamar directamente a `/src/encargossacd/ctr_ficha_update`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$campos = $_POST;
unset($campos['hh']);

PostRequest::getDataFromUrl('/src/encargossacd/ctr_ficha_update', $campos);

header('Content-Type: text/plain; charset=UTF-8');
echo '';
