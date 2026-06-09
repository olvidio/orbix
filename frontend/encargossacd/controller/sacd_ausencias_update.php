<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy frontend a `/src/encargossacd/sacd_ausencias_update`
 * ({@see \src\encargossacd\application\SacdAusenciasUpdate}).
 *
 * El JS consumidor (`sacd_ausencias_get.phtml`) espera texto plano: muestra
 * `alert(rta_txt)` cuando el cuerpo no esta vacio y dispara `js_atras(1)` si el
 * cuerpo esta vacio. Mantenemos esa semantica: `PostRequest::getDataFromUrl`
 * hace `exit` con los mensajes si el backend marca `success=false`; en exito
 * devolvemos un cuerpo vacio.
 *
 * Cuando el JS migre a leer la respuesta JSON directamente, este proxy podra
 * eliminarse y apuntar el form a `/src/encargossacd/sacd_ausencias_update`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$campos = $_POST;
unset($campos['hh']);

PostRequest::getDataFromUrl('/src/encargossacd/sacd_ausencias_update', $campos);

header('Content-Type: text/plain; charset=UTF-8');
echo '';
