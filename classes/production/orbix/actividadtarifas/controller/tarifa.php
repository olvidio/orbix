<?php
// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$txt_eliminar = _("¿Está seguro de borrar esta tarifa?");

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
$oHash->setCamposForm('que');
$h_ver = $oHash->linkSinVal();

$oHashMod = new Hash();
$oHashMod->setUrl(ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
$oHashMod->setCamposForm('que!id_tarifa');
$h_modificar = $oHashMod->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_modificar' => $h_modificar,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewPhtml('actividadtarifas\controller');
$oView->renderizar('tarifa.phtml', $a_campos);