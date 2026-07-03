<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\misas\helpers\MisasDesplegableSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/misas/modificar_iniciales_sacd_zona_data');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($data['a_opciones'] ?? []));
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_iniciales_sacd_zona()');

$url_ver_iniciales_zona = 'frontend/misas/controller/ver_iniciales_zona.php';
$oHashZona = new HashFront();
$oHashZona->setUrl($url_ver_iniciales_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinValParams();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'url_ver_iniciales_zona' => $url_ver_iniciales_zona,
    'h_zona' => $h_zona,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'modificar_iniciales_sacd_zona.phtml', $a_campos);
