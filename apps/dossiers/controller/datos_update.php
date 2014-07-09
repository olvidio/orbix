<?php
use dossiers\model as dossiers;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$s_pkey=explode('#',$_POST['sel'][0]);
	// si he cambiado las comillas dobles por simples. Deshago el cambio.
	$s_pkey[0] = str_replace("'",'"',$s_pkey[0]);
	$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey[0]));
} else {
	empty($_POST['s_pkey'])? $s_pkey="" : $s_pkey=$_POST['s_pkey'];
	$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey));
}
/***************  datos  **********************************/
$padre='datos_update'; // para indicarle al $dir_datos lo que quiero.
$dir_datos=core\ConfigGlobal::$dir_web."/apps/dossiers/model/datos_${_POST['id_dossier']}.php";
include($dir_datos);

//------------ BORRAR --------
if ($_POST['mod']=="eliminar") {
	if ($oFicha->DBEliminar() === false) {
		echo _('Hay un error, no se ha eliminado');
	} else {
 		// hay que cerrar el dossier para esta persona/actividad/ubi, si no tiene más:
		$Coleccion=$oGestor->getTelecosUbi(array('id_ubi'=>$_POST['id_pau']));
		if (empty($Coleccion)) {
			$oDossier = new dossiers\Dossier(array('tabla'=>$_POST['pau'],'id_pau'=>$_POST['id_pau'],'id_tipo_dossier'=>$_POST['id_dossier']));
			$oDossier->cerrar();
			$oDossier->DBGuardar();
		}
	}
}
//------------ NUEVO --------
if ($_POST['mod']=="nuevo") {
	foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
		$nom_camp=$oDatosCampo->getNom_camp();	
		$_POST[$nom_camp]=empty($_POST[$nom_camp])? '' : $_POST[$nom_camp];
		$oFicha->$nom_camp=$_POST[$nom_camp];
	}
	if ($oFicha->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	} else {
		// si no está abierto, hay que abrir el dossier para esta persona/actividad/ubi
		$oDossier = new dossiers\Dossier(array('tabla'=>$_POST['pau'],'id_pau'=>$_POST['id_pau'],'id_tipo_dossier'=>$_POST['id_dossier']));
		$oDossier->abrir();
		$oDossier->DBGuardar();
	}
} 
//------------ EDITAR --------
if ($_POST['mod']=="editar") {
	$oFicha->DBCarregar();
	foreach ($a_pkey as $key=>$val) {
		$oFicha->$key=$val;
	}
	foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
		$nom_camp=$oDatosCampo->getNom_camp();	
		$oFicha->$nom_camp=$_POST[$nom_camp];
	}
	if ($oFicha->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	} else {
		// si no está abierto, hay que abrir el dossier para esta persona/actividad/ubi
		$oDossier = new dossiers\Dossier(array('tabla'=>$_POST['pau'],'id_pau'=>$_POST['id_pau'],'id_tipo_dossier'=>$_POST['id_dossier']));
		$oDossier->abrir();
		$oDossier->DBGuardar();
	}
}
?>
