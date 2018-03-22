<?php

use core\ConfigGlobal;
use dossiers\model as dossiers;
use personas\model as personas;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error = '';

$id_pau  = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
$oPersonaDl = new personas\PersonaDl($id_pau);
$oPersonaDl->DBCarregar();

//centro
$new_ctr  = empty($_POST['new_ctr'])? '' : $_POST['new_ctr'];
$f_ctr  = empty($_POST['f_ctr'])? '' : $_POST['f_ctr'];

if (!empty($new_ctr) AND !empty($f_ctr)){
	$id_ctr_o  = empty($_POST['id_ctr_o'])? '' : $_POST['id_ctr_o'];
	$ctr_o  = empty($_POST['ctr_o'])? '' : $_POST['ctr_o'];

	$id_new_ctr=strtok($new_ctr,"#");
	$nom_new_ctr=strtok("#");

	$oPersonaDl->setId_ctr($id_new_ctr);
	// ?? $oPersonaDl->setF_ctr($f_ctr);
	if ($oPersonaDl->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}

  	//para el dossier de traslados
 	$oTraslado = new personas\Traslado();
	$oTraslado->setId_nom($id_pau);
	$oTraslado->setF_traslado($f_ctr);
	$oTraslado->setTipo_cmb('sede');
	$oTraslado->setId_ctr_origen($id_ctr_o);
	$oTraslado->setCtr_origen($ctr_o);
	$oTraslado->setId_ctr_destino($id_new_ctr);
	$oTraslado->setCtr_destino($nom_new_ctr);
	if ($oTraslado->DBGuardar() === false) {
		$error .= '<br>'._('Hay un error, no se ha guardado');
	}
}

//cambio de dl
$old_dl = $oPersonaDl->getDl();
$new_dl  = empty($_POST['new_dl'])? '' : $_POST['new_dl'];
$f_dl  = empty($_POST['f_dl'])? '' : $_POST['f_dl'];
$situacion  = empty($_POST['situacion'])? '' : $_POST['situacion'];
$reg_dl_org  = empty($_POST['dl'])? '' : ConfigGlobal::mi_region().'-'.$_POST['dl'];
$sfsv_txt = (configGlobal::mi_sfsv() == 1)? 'v' :'f';

if (!empty($new_dl) AND !empty($f_dl)){
	$reg_dl_org  .= $sfsv_txt;
	$new_dl  .= $sfsv_txt;
	$oTrasladoDl = new personas\trasladoDl();
	$oTrasladoDl->setId_nom($id_pau);
	$oTrasladoDl->setDl_persona($old_dl);
	$oTrasladoDl->setReg_dl_org($reg_dl_org);
	$oTrasladoDl->setReg_dl_dst($new_dl);
	$oTrasladoDl->setF_dl($f_dl);
	$oTrasladoDl->setSituacion($situacion);

	$error = $oTrasladoDl->trasladar();
}


// hay que abrir el dossier para esta persona/actividad/ubi, si no tiene.
$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_pau,'id_tipo_dossier'=>1004));
$oDossier->abrir(); // ya pone la fecha de hoy.
$oDossier->DBGuardar();

if (empty($error)) {
	$oPosicion->setId_div('ir_a');
	echo $oPosicion->mostrar_left_slide();
} else {
	echo $error;
}