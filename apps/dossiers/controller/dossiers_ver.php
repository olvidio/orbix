<?php

use actividades\model as actividades;
use core\ConfigGlobal;
use dossiers\model as dossiers;
use personas\model as personas;
use web\Hash;
use web\Posicion;
//use core;
//use web;
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

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$stack = (integer)  \filter_input(INPUT_POST, 'stack');
//Si vengo por medio de Posicion, borro la última
if (!empty($stack)) {
	// No me sirve el de global_object, sino el de la session
	$oPosicion2 = new Posicion();
	if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
		$Qid_sel=$oPosicion2->getParametro('id_sel');
		$Qscroll_id = $oPosicion2->getParametro('scroll_id');
		$oPosicion2->olvidar($stack);
	}
}
$oPosicion->recordar();
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// el scroll id es de la página anterior, hay que guardarlo allí
if (!empty($a_sel)) { //vengo de un checkbox
 	$id_sel=$a_sel;
	$id_pau=strtok($a_sel[0],"#");
	$id_tabla=strtok("#");
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	empty($_POST['id_pau'])? $id_pau='' : $id_pau=$_POST['id_pau'];
}
if (empty($pau)) $pau=$_POST['pau'];
if (empty($permiso) && !empty($_POST['permiso'])) {
	$permiso=$_POST['permiso'];
} else {
	$permiso="";
}
$obj_pau = empty($_POST['obj_pau'])? '' : $_POST['obj_pau'];

$_POST['go_atras'] = empty($_POST['go_atras'])? '' : $_POST['go_atras'];
// para las tablas que estan en el exterior:
//$oDB = que_DB($_POST['obj_pau']);

// según sean personas, ubis o actividades:
switch ($pau) {
	case 'p':
		$top="top_personas";
		$ficha="ficha_personas";
		//Hay que aclararse si la persona es de la dl o no
		if ($obj_pau == 'Persona') {
			$oPersona = personas\Persona::NewPersona($id_pau);
			if (!is_object($oPersona)) {
				$msg_err = "<br>$oPersona con id_nom: $id_pau";
				exit($msg_err);
			}
			$clase = get_class($oPersona);
			$obj_pau = join('', array_slice(explode('\\', $clase), -1));
		} else {
			$clase = "personas\\model\\$obj_pau";
			$oPersona = new $clase($id_pau);
		}
		$nom = $oPersona->getNombreApellidos();

		$goficha=Hash::link(ConfigGlobal::getWeb().'/apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras']))); 
		$godossiers=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras'])));
		$form_action=$_POST['go_atras'];
		break;
	case 'u':
		$top="top_ubis";
		$ficha="ficha_ubis";
		$clase = "ubis\\model\\$obj_pau";
		$oUbi = new $clase($id_pau);
		$nom = $oUbi->getNombre_ubi();
		
		$goficha=Hash::link(ConfigGlobal::getWeb().'/apps/ubis/controller/home_ubis.php?'.http_build_query(array('id_ubi'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras'])));
		$godossiers=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras'])));
		
		if (!empty($id_direccion)) {
			$goficha.='&id_direccion='.$id_direccion;
			$godossiers.='&id_direccion='.$id_direccion;
		}	
		if (!empty($tipo)) {
			$goficha.='&tipo='.$tipo;
			$godossiers.='&tipo='.$tipo;
		}	
		if (!empty($sin_dir)) {
			$goficha.='&sin_dir='.$sin_dir;
			$godossiers.='&sin_dir='.$sin_dir;
		}	
		
		// si vengo de los listados se scdl
		if (isset($_SESSION['session_go_to']) ) {
			//$_POST['go_atras']=$_SESSION['session_go_to']['sel']['go_atras'];
		}

		if (empty($_POST['go_atras'])) {
			$form_action=Hash::link(ConfigGlobal::getWeb().'/apps/ubis_tabla.php');
		} else {
			$form_action=$_POST['go_atras'];
		}
		break;
	case 'a':
		$top="top_activ";
		$ficha="ficha_activ";
		$oActividad  = new actividades\Actividad($id_pau);
		$nom = $oActividad->getNom_activ();
		$goficha=Hash::link(ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_ver.php?'.http_build_query(array('id_activ'=>$id_pau,'tabla'=>$obj_pau,'go_atras'=>$_POST['go_atras']))); 
		$godossiers=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'go_atras'=>$_POST['go_atras'])));
		// según de donde venga, debo volver al mismo sitio...
		if (!empty($_SESSION['session_go_to']['sel']['pag'])) {
			$pag = $_SESSION['session_go_to']['sel']['pag']; //=>"lista_actividades_sg.php",
			$dir = $_SESSION['session_go_to']['sel']['dir_pag']; //=>core\ConfigGlobal::$directorio."/sg",
			$dir = str_replace(ConfigGlobal::$directorio,'',$dir);
			$form_action=Hash::link(ConfigGlobal::getWeb()."$dir/$pag");
		} else {
			$form_action=Hash::link(ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select.php');
		}
//		$id_sel = array("$id_pau#$nom");
//		$oPosicion->addParametro('id_sel',$id_sel,1);
		break;
}

//echo "qq: $q_nom<br>";

$go_to= empty($go_to)? 'session@sel' : $go_to; //voy a buscar la última lista de seleccionados
$alt=_("ver dossiers");
$dos=_("dossiers");
$txt="<span class=link onclick=fnjs_update_div('#main','$goficha')>$nom</span>";
$titulo=$txt;

// -----------------------------  cabecera ---------------------------------

?>
<?= $oPosicion->mostrar_left_slide(1); ?>
<div id=<?= $top ?>>
<table><tr>
<td><span class=link onclick= fnjs_update_div('#main','<?= $godossiers ?>')><img src=<?= ConfigGlobal::$web_icons ?>/dossiers.gif border=0 width=40 height=40 alt='<?= $alt ?>'>(<?= $dos ?>)</span></td>
<td class=titulo><?= $titulo ?></td>
</table>
</div>
<?php
// -------------------------------------------------------------------------
if (empty($_POST['queSel'])) $_POST['queSel']='';

switch ($_POST['queSel']){
	case "activ": // actividades de un asistente
		$pau="p";
		$permiso=3;
		empty($_POST['id_dossier'])? $id_dossier="" : $id_dossier=$_POST['id_dossier'];
		break;
	case "matriculas": // actividades de un asistente
		$pau="p";
		$permiso=3;
		empty($_POST['id_dossier'])? $id_dossier="" : $id_dossier=$_POST['id_dossier'];
		break;
	case "asis": // asistentes a una actividad
		$pau="a";
		$permiso=3;
		$id_dossier=3101;
		break;
	case "asig": // asignaturas de una actividad
		$pau="a";
		$permiso=3;
		$id_dossier=3005;
		break;
	case "carg":
		$pau="a";
		$permiso=3;
		$id_dossier=3102;
		break;
	default: // enseña la lista de dossiers.
	//	$id_dossier="";
		empty($_POST['id_dossier'])? $id_dossier="" : $id_dossier=$_POST['id_dossier'];
}
if (!empty($accion)) {
	cerrar_dossier($pau,$id_pau,$id_tipo_dossier,$oDB);
	$go_to="dossiers_lista.php?pau=$pau&id_pau=$id_pau&obj_pau=".$obj_pau."";
	ir_a($go_to);
}

// ------------------------- cuerpo -----------------------------
echo "<div id=$ficha>";
//echo "id_dossier: $id_dossier<br>";
if (empty($id_dossier)) { // enseña la lista de dossiers.
	include ("lista_dossiers.php");
} else {
	// Voy a intentar mostrar dossiers seguidos. Se supone que id_dossier es una lista de nº separados por 'y'
	$id_dossier=strtok($id_dossier,"y");
	while  ($id_dossier) {
		$oTipoDossier = new dossiers\TipoDossier($id_dossier);
		$tabla_dossier=$oTipoDossier->getTabla_to();
		$app=$oTipoDossier->getApp();

		// según sean personas, ubis o actividades:
		switch ($pau) {
			case 'p':
				$condicion="Where id_nom=$id_pau";
				//$id_pau="id_nom";
				break;
			case 'u':
				$condicion="Where id_ubi=$id_pau";
				break;
			case 'a':
				$condicion="Where id_activ=$id_pau";
				break;
		}
		
		// para el botón editar en la presentación general...
		if ($permiso==3) { $edit=1; }
		// Para presentaciones particulares
		//$pres_2="../model/datos_".$id_dossier.".php";
		//$pres="./sql_".$id_dossier.".php";
		$pres_2="../../$app/model/datos_".$id_dossier.".php";
		$pres="../../$app/controller/sql_".$id_dossier.".php";

		/* GOTO */
		switch($id_dossier) {
			case 1303:
				$go_to=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>$id_dossier,'permiso'=>$permiso,'que'=>'matriculas')));
				break;
			case 3103: //matriculas de un ca
				$go_to=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>$id_dossier,'permiso'=>$permiso,'queSel'=>'asis')));
				break; //nada, ya esta en el sql_1303
			default:
				$go_to=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau,'id_dossier'=>$id_dossier,'permiso'=>$permiso)));
				break; //nada, ya esta en el sql_3101
		}

		if (realpath($pres_2)){ //como file_exists
			include ("datos_sql.php");
		} elseif (realpath($pres)){
			include ($pres);
		} 
		
		// Poner o no el botón de inserta. En algunos casos ya está en la presentación particular.
		// miro los permisos:
		if ($permiso==3 && !file_exists($pres_2)){ 
			switch($id_dossier) {
				case 1004: //traslados de ctr o dl
					$insert=Hash::link(ConfigGlobal::getWeb().'/apps/personas/controller/traslado_form.php?'.http_build_query(array('cabecera'=>'no','id_pau'=>$id_pau,'id_dossier'=>$id_dossier,'obj_pau'=>$obj_pau,'go_to'=>$go_to)));
					echo "<p><span class=link onclick=fnjs_update_div('#main','$insert')>"._("insertar")."</span></p>";
					break;
				case 1303:
				case 3103: //matriculas de un ca
					break; //nada, ya esta en el sql_1303
				case 1201:
				case 2102: //cargos de un ctr
					break; //nada, ya esta en el sql_2102
				case 3101: //asistentes a un ca
					break; //nada, ya esta en el sql_3101
			}
			//para el botón cerrar dossier:
			$cerrar=Hash::link(ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('accion'=>'cerrar','pau'=>$pau,'id_pau'=>$id_pau,'id_tipo_dossier'=>$id_dossier,'obj_pau'=>$obj_pau)));
			$etiqueta_cerrar= "<br><br><span class=link onclick=fnjs_update_div('#main','$cerrar')>cerrar dossier</span>";
		} // fin del if permiso
		$id_dossier=strtok("y");
	} //fin del while
} // fin del if que.
//echo $etiqueta_cerrar;
echo "</div>";