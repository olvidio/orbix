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
$oDesplZonas->setAction('fnjs_ver_plantilla_zona()');

$a_TiposPlantilla= array('s'=>'semanal', 'd'=>'semanal y domingos', 'm'=>'mensual');
$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('TipoPlantilla');
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

//$url_ver_plantilla_zona = 'apps/misas/controller/crear_plantilla.php';
$url_ver_plantilla_zona = 'apps/misas/controller/ver_plantilla_zona.php';
$oHashZonaTipo = new Hash();
$oHashZonaTipo->setUrl($url_ver_plantilla_zona);
$oHashZonaTipo->setCamposForm('id_zona!TipoPlantilla');
$h_zonatipo = $oHashZonaTipo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'url_ver_plantilla_zona' => $url_ver_plantilla_zona,
    'h_zona' => $h_zonatipo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('seleccionar_zona_tipo.html.twig', $a_campos);
//echo $oView->render('seleccionar_zona.html.twig', $a_campos);