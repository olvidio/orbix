<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\misas\helpers\MisasDesplegableSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// Si el use case devuelve error (p.ej. permiso denegado), PostRequest hace
// `exit()` con el mensaje antes de devolver el control aqui.
$data = PostRequest::getDataFromUrl('/src/misas/modificar_encargos_centros_data');

$a_opciones_zona = $data['a_opciones_zona'] ?? [];

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($a_opciones_zona));
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_encargos_centros()');

$url_ver_encargos_centros = 'frontend/misas/controller/ver_encargos_centros.php';
$oHashZona = new HashFront();
$oHashZona->setUrl($url_ver_encargos_centros);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinValParams();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'url_ver_encargos_centros' => $url_ver_encargos_centros,
    'h_zona' => $h_zona,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'modificar_encargos_centros.phtml', $a_campos);
