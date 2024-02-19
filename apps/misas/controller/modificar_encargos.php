<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use web\Desplegable;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_encargos_zona()');

$url_ver_encargos_zona = 'apps/misas/controller/ver_encargos_zona.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_ver_encargos_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'url_ver_encargos_zona' => $url_ver_encargos_zona,
    'h_zona' => $h_zona,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_encargos.html.twig', $a_campos);