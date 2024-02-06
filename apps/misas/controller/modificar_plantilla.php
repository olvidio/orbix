<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use misas\domain\entity\EncargoDia;
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

$a_TiposPlantilla= array(EncargoDia::PLANTILLA_SEMANAL=>'semanal', EncargoDia::PLANTILLA_DOMINGOS=>'semanal y domingos', EncargoDia::PLANTILLA_MENSUAL=>'mensual');
$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('tipo_plantilla');
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaTipo = new Hash();
$oHashZonaTipo->setUrl($url_ver_cuadricula_zona);
$oHashZonaTipo->setCamposForm('id_zona!tipo_plantilla!seleccion');
$h_zonatipo = $oHashZonaTipo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona' => $h_zonatipo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_plantilla.html.twig', $a_campos);