<?php
use personas\model as personas;
use ubis\model as ubis;
/**
* Esta página pone el titulo en el frame superior.
*
*
*@package	delegacion
*@subpackage	dossiers
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Si vengo de un link antiguo:
//if (!empty($tabla)) $tabla_pau=$tabla;


if (!empty($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
	$id_tabla = empty($_POST['id_tabla'])? '' : $_POST['id_tabla'];
}


if (!empty($id_tabla)) {
	switch ($id_tabla) {
		case "n":
			$obj_pau="PersonaN";
			break;	
		case "x":
			$obj_pau="PersonaNax";
			break;	
		case "a":
			$obj_pau="PersonaAgd";
			break;
		case "s":
			$obj_pau="PersonaS";
			break;	
		case "sssc":
			$obj_pau="PersonaSSSC";
			break;	
		case "pn":
		case "pa":
		case "psssc":
			$obj_pau="PersonaEx";
			break;
	}
} else {
	empty($_POST['obj_pau'])? $obj_pau="" : $obj_pau=$_POST['obj_pau'];
}

// Si vengo de planning_select u otros, puede que la tabla sea más genérica (p_de_casa) y no sepa como resolver algunas cosas.
if (isset($_SESSION['session_go_to']['sel']['tabla'])) {
	$_SESSION['session_go_to']['sel']['tabla']=$obj_pau;
}

$id_pau = $id_nom;
$pau="p";

/* def variables **/
$select="";
$select_agd="";
$select_super="";
$select_cp="";
$select_cp_ae="";
$select_sssc="";
$select_de_paso="";
$from="";

// según sean numerarios...
$obj = 'personas\\model\\'.$obj_pau;
$oPersona = new $obj($id_nom);


$nom = $oPersona->getNombreApellidos();
$dl = $oPersona->getDl();
$lengua = $oPersona->getLengua();
$f_nacimiento = $oPersona->getF_nacimiento();
$santo = '';
$celebra = '';
$situacion = $oPersona->getSituacion();
$f_situacion = $oPersona->getF_situacion();
$profesion = $oPersona->getProfesion();
$stgr = $oPersona->getStgr();
if ($obj_pau != 'PersonaEx') {
	$id_ctr = $oPersona->getId_ctr();
	$oCentroDl = new ubis\CentroDl($id_ctr);	
	$ctr = $oCentroDl->getNombre_ubi();
} else {
	$ctr = '';
}

//if (empty($_POST['go_atras'])) { $go_atras="programas/personas_select.php"; } else { $go_atras=$_POST['go_atras']; }
$gohome=web\Hash::link("apps/personas/controller/home_persona.php?id_nom=$id_nom&obj_pau=$obj_pau"); 
$godossiers=web\Hash::link("apps/dossiers/controller/dossiers_ver.php?pau=$pau&id_pau=$id_nom&obj_pau=$obj_pau");
$go_breve=web\Hash::link("apps/personas/controller/personas_editar.php?id_nom=$id_nom&obj_pau=$obj_pau&breve=true"); 
$go_ficha=web\Hash::link("apps/personas/controller/personas_editar.php?id_nom=$id_nom&obj_pau=$obj_pau"); 

$alt=_("ver dossiers");
$dos=_("dossiers");
$titulo=$nom;

//$telfs_fijo = telecos_persona($id_nom,"telf","*"," / ") ;
//$telfs_movil = telecos_persona($id_nom,"móvil","*"," / ") ;
//if ($telfs_fijo && $telfs_movil) { $telfs = $telfs_fijo ." / ". $telfs_movil; } else { $telfs = $telfs_fijo . $telfs_movil;} 
$telfs = '';

?>
<div id="top_personas"  name="top_personas"><?php include ("../view/home_persona.phtml"); ?></div>
<div id="ficha_personas" name="ficha_personas"><?php include ("apps/dossiers/controller/lista_dossiers.php"); ?></div>
