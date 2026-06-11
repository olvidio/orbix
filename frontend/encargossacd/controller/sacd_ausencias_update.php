<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

use frontend\shared\FrontBootstrap;

/**
 * Proxy frontend a `/src/encargossacd/sacd_ausencias_update`
 * ({@see \src\encargossacd\application\SacdAusenciasUpdate}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$campos = $_POST;
unset($campos['hh']);

ajax_json_proxy_post_request('/src/encargossacd/sacd_ausencias_update', $campos);
