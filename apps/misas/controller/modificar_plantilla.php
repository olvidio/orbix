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

$a_TiposPlantilla = array(
    EncargoDia::PLANTILLA_SEMANAL_UNO=>'semanal una opción',
    EncargoDia::PLANTILLA_DOMINGOS_UNO=>'semanal y domingos una opción',
    EncargoDia::PLANTILLA_MENSUAL_UNO=>'mensual una opción',
    EncargoDia::PLANTILLA_SEMANAL_TRES=>'semanal tres opciones',
    EncargoDia::PLANTILLA_DOMINGOS_TRES=>'semanal y domingos tres opciones',
    EncargoDia::PLANTILLA_MENSUAL_TRES=>'mensual tres opciones',
);

$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('tipo_plantilla');
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_plantilla_zona()');


$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaTipo = new Hash();
$oHashZonaTipo->setUrl($url_ver_cuadricula_zona);
$oHashZonaTipo->setCamposForm('id_zona!tipo_plantilla!orden');
$h_zonatipo = $oHashZonaTipo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oDesplOrden' => $oDesplOrden,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona' => $h_zonatipo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_plantilla.html.twig', $a_campos);