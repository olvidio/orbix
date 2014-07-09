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
		echo _('Hay un error, no se ha guardado');
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
		echo _('Hay un error, no se ha guardado');
	}
}

//cambio de dl
$new_dl  = empty($_POST['new_dl'])? '' : $_POST['new_dl'];
$f_dl  = empty($_POST['f_dl'])? '' : $_POST['f_dl'];
if (!empty($new_dl) AND !empty($f_dl)){
	echo ('de moment no faig res');
	/*
	$nom_new_dl=strtok($_POST['new_dl'],"#");
	$region_new_dl=strtok("#");
	$query = "UPDATE ".$_POST['tabla_pau']." SET dl='$nom_new_dl', fichero='".$_POST['fichero']."', f_fichero='".$_POST['f_dl']."' WHERE id_nom=".$_POST['id_pau'];
	//para el dossier de traslados

	$_POST['id_c_o_cr']="" ? $id_c_o = $_POST['id_c_o_cr'] : $id_c_o="NULL"  ; //si no existe, pongo valor nulo
	empty($_POST['dl'])? $ctr_o="" : $ctr_o=$_POST['dl'];
	$id_c_d="NULL";

	$ctr_d= AddSlashes($nom_new_dl);
	$valores=$_POST['id_pau'].",'".$_POST['f_dl']."','dl',$id_c_o,'$ctr_o',$id_c_d,'$ctr_d'";
	$valores = str_replace("''","NULL",$valores);
	$query_d= "INSERT INTO d_traslados 
			 (id_nom,f_traslado,tipo_cmb,id_ctr_origen,ctr_origen,id_ctr_destino,ctr_destino)
	  VALUES ($valores)";

	//ejecuta
	$oDBSt_r=$oDB->query($query);
	$oDBSt_r=$oDB->query($query_d);
	//if ($r>0) { $rr=abrir_dossier('p',$_POST['id_pau'],1004,$oDB); }
	*/
}


// hay que abrir el dossier para esta persona/actividad/ubi, si no tiene.
$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_pau,'id_tipo_dossier'=>1004));
$oDossier->abrir(); // ya pone la fecha de hoy.
$oDossier->DBGuardar();

$oPosicion->setId_div('ir_a');
echo $oPosicion->atras();
?>
