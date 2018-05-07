<?php
use personas\model\entity as personas;
use ubis\model\entity as ubis;
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

$oPosicion->recordar();

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$id_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
	$id_tabla = (string) \filter_input(INPUT_POST, 'id_tabla');
	
	$id_sel = array("$id_nom#$id_tabla");
	$oPosicion->addParametro('id_sel',$id_sel);
}


if (!empty($id_tabla)) {
	switch ($id_tabla) {
		case "n":
			$Qobj_pau="PersonaN";
			break;	
		case "x":
			$Qobj_pau="PersonaNax";
			break;	
		case "a":
			$Qobj_pau="PersonaAgd";
			break;
		case "s":
			$Qobj_pau="PersonaS";
			break;	
		case "sssc":
			$Qobj_pau="PersonaSSSC";
			break;	
		case "pn":
		case "pa":
		case "psssc":
			$Qobj_pau="PersonaEx";
			break;
	}
} else {
	$Qobj_pau = (string) filter_input(INPUT_POST,'obj_pau');
}

// Si vengo de planning_select u otros, puede que la tabla sea más genérica (p_de_casa) y no sepa como resolver algunas cosas.
if (isset($_SESSION['session_go_to']['sel']['tabla'])) {
	$_SESSION['session_go_to']['sel']['tabla']=$Qobj_pau;
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
$obj = 'personas\\model\\entity\\'.$Qobj_pau;
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
if ($Qobj_pau != 'PersonaEx' && $Qobj_pau != 'PersonaIn') {
	$id_ctr = $oPersona->getId_ctr();
	$oCentroDl = new ubis\CentroDl($id_ctr);	
	$ctr = $oCentroDl->getNombre_ubi();
} else {
	$ctr = '';
}

$gohome=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$Qobj_pau))); 
$godossiers=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_nom,'obj_pau'=>$Qobj_pau)));
$go_breve=web\Hash::link('apps/personas/controller/personas_editar.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$Qobj_pau,'breve'=>'true'))); 
$go_ficha=web\Hash::link('apps/personas/controller/personas_editar.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$Qobj_pau))); 

$alt=_("ver dossiers");
$dos=_("dossiers");
$titulo=$nom;

//$telfs_fijo = telecos_persona($id_nom,"telf","*"," / ") ;
//$telfs_movil = telecos_persona($id_nom,"móvil","*"," / ") ;
//if ($telfs_fijo && $telfs_movil) { $telfs = $telfs_fijo ." / ". $telfs_movil; } else { $telfs = $telfs_fijo . $telfs_movil;} 
$telfs = '';

?>
<div id="top"  name="top"><?php include ("../view/home_persona.phtml"); ?></div>
<div id="ficha" name="ficha"><?php include ("apps/dossiers/controller/lista_dossiers.php"); ?></div>
