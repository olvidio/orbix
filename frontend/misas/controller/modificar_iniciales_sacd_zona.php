<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/misas/modificar_iniciales_sacd_zona_data');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($data['a_opciones'] ?? []);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_iniciales_sacd_zona()');

$url_ver_iniciales_zona = 'frontend/misas/controller/ver_iniciales_zona.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_ver_iniciales_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinVal();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'url_ver_iniciales_zona' => $url_ver_iniciales_zona,
    'h_zona' => $h_zona,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('modificar_iniciales_sacd_zona.phtml', $a_campos);
