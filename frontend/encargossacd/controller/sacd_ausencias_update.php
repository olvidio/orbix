<?php

use frontend\shared\helpers\AjaxJsonSupport;
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

AjaxJsonSupport::proxyPostRequest('/src/encargossacd/sacd_ausencias_update', $campos);
