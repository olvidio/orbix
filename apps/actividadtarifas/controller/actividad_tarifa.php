<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$txt_eliminar = _("¿Está seguro de borrar esta tarifa?");

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/actividadtarifas/controller/actividad_tarifa_ajax.php');
$oHash->setCamposForm('que');
$h_ver = $oHash->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl(core\ConfigGlobal::getWeb().'/apps/actividadtarifas/controller/actividad_tarifa_ajax.php');
$oHashMod->setCamposForm('que!id_tarifa');
$h_modificar = $oHashMod->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
		'h_ver' => $h_ver,
		'h_modificar' => $h_modificar,
		'txt_eliminar' => $txt_eliminar,
		];

$oView = new core\View('actividadtarifas/controller');
echo $oView->render('actividad_tarifa.phtml',$a_campos);