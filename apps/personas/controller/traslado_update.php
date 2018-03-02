<?php

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
$dl_o  = empty($_POST['dl'])? '' : $_POST['dl'];

if (!empty($new_dl) AND !empty($f_dl)){
	$TrasladoDl = new personas\trasladoDl();
	$TrasladoDl->setId_nom($id_pau);
	$TrasladoDl->setDl_persona($old_dl);
	$TrasladoDl->setDl_org($dl_o);
	$TrasladoDl->setReg_dl_dst($new_dl);
	$TrasladoDl->setF_dl($f_dl);
	$TrasladoDl->setSituacion($situacion);

	if ($TrasladoDl->comprobar() === false) {
		exit (_("Ya esta trasladado. No se ha hecho ningún cambio."));
	}
	// Aviso si le faltan notas
	if ($TrasladoDl->comprobarNotas() !== true) {
		$error .= $TrasladoDl->comprobarNotas();
	}

	// Trasladar persona
	// Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo
	// tener la misma persona en dos dl en la misma situación
	if ($TrasladoDl->cambiarFichaPersona() !== true) {
		exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));
	}

	$TrasladoDl->copiarPersona();

	$TrasladoDl->copiarNotas();
	// apunto el traslado. Lo pongo antes para que se copie trasladar dossiers.
	$TrasladoDl->apuntar();
	$TrasladoDl->trasladarDossiers();
}


// hay que abrir el dossier para esta persona/actividad/ubi, si no tiene.
$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_pau,'id_tipo_dossier'=>1004));
$oDossier->abrir(); // ya pone la fecha de hoy.
$oDossier->DBGuardar();

if (empty($error)) {
	$oPosicion->setId_div('ir_a');
	echo $oPosicion->atras();
} else {
	echo $error;
}