<?php

use actividades\model\entity as actividades;
use core\ConfigGlobal;
use dossiers\model\entity as dossiers;
use personas\model\entity as personas;
use web\Hash;
use web\Posicion;

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Si vengo de eliminar, hay que borrar el 'sel' que ha identificado el registro,
//  pues ya no existe
$Qmod = (string)  \filter_input(INPUT_POST, 'mod');
if (isset($a_sel) && ($Qmod == 'eliminar' OR $Qmod == 'nuevo')) {
	unset($a_sel);
}

$Qid_sel = '';
$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} elseif (!empty($a_sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$Qid_sel = $a_sel;
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$Qscroll_id,1);
}
	
$Qid_pau = (integer)  \filter_input(INPUT_POST, 'id_pau');
$pau = (string)  \filter_input(INPUT_POST, 'pau');
$Qobj_pau = (string)  \filter_input(INPUT_POST, 'obj_pau');
$Qid_dossier = (string)  \filter_input(INPUT_POST, 'id_dossier');
$Qpermiso = (string)  \filter_input(INPUT_POST, 'permiso');
$QqueSel = (string)  \filter_input(INPUT_POST, 'queSel');

// si vengo de modificar el dossier, 
//			$clase_info = "$app\\model\\entity\\datos$id_dossier";
$Qclase_info = (string)  \filter_input(INPUT_POST, 'clase_info');
if (empty($Qid_dossier) && !empty($Qclase_info)) {
	// Tiene que ser en dos pasos.
	$obj = urldecode($Qclase_info);
	$oInfoClase = new $obj();
	$Qid_dossier = $oInfoClase->getId_dossier();
	$pau = $oInfoClase->getPau();
}

/*
//No está claro quien manda. En actividades de una persona, cunado se hace actualizar, 
// no importa que este alguna seleccionada, se debe mantener el id_pau original...
 * */
// Si vengo de actualizar (Qrefresh) No hay que hacer caso del $a_sel, puede estar seleccionada cualquier cosa
if (!empty($Qrefresh)) {
	$id_pau = $Qid_pau;
} elseif (!empty($a_sel)) {
    $id_pau= (integer) strtok($a_sel[0],"#");
} else {
	$id_pau = $Qid_pau;
	
}

switch ($QqueSel){
	case "activ": // actividades de un asistente
		$pau="p";
		$Qpermiso=3;
		break;
	case "matriculas": // actividades de un asistente
		$pau="p";
		$Qpermiso=3;
		// En asistentes de un ca, ver plan estudios, Machaco el id_pau que tenga (que es id_activ)
		$Qid_activ = $Qid_pau;
//		if (!empty($a_sel)) { //vengo de un checkbox
//			$id_pau= strtok($a_sel[0],"#");
//		} else {
//			$id_pau = $Qid_pau;
//		}
		break;
	case "asis": // asistentes a una actividad
		$pau="a";
		$Qpermiso=3;
		$Qid_dossier=3101;
		break;
	case "asig": // asignaturas de una actividad
		$pau="a";
		$Qpermiso=3;
		$Qid_dossier=3005;
		break;
	case "carg":
		$pau="a";
		$Qpermiso=3;
		$Qid_dossier=3102;
		break;
	default: // enseña la lista de dossiers.
}


$sQuery = http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$Qobj_pau));
$godossiers = Hash::link(ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?$sQuery");
// según sean personas, ubis o actividades:
switch ($pau) {
	case 'p':
		//Hay que aclararse si la persona es de la dl o no
		if ($Qobj_pau == 'Persona') {
			$oPersona = personas\Persona::NewPersona($id_pau);
			if (!is_object($oPersona)) {
				$msg_err = "<br>$oPersona con id_nom: $id_pau en  ".__FILE__.": line ". __LINE__;
				exit($msg_err);
			}
			$clase = get_class($oPersona);
			$Qobj_pau = join('', array_slice(explode('\\', $clase), -1));
		} else {
			$clase = "personas\\model\\entity\\$Qobj_pau";
			$oPersona = new $clase($id_pau);
		}
		$nom_cabecera = $oPersona->getNombreApellidos();

		$sQuery = http_build_query(array('id_nom'=>$id_pau,'obj_pau'=>$Qobj_pau)); 
		$goHome=Hash::link(ConfigGlobal::getWeb()."/apps/personas/controller/home_persona.php?$sQuery"); 
		
		break;
	case 'u':
		$clase = "ubis\\model\\entity\\$Qobj_pau";
		$oUbi = new $clase($id_pau);
		$nom_cabecera = $oUbi->getNombre_ubi();
		
		$sQuery = http_build_query(array('id_ubi'=>$id_pau,'obj_pau'=>$Qobj_pau));
		$goHome = Hash::link(ConfigGlobal::getWeb()."/apps/ubis/controller/home_ubis.php?$sQuery");
//		
//		if (!empty($id_direccion)) {
//			$goHome.='&id_direccion='.$id_direccion;
//			$godossiers.='&id_direccion='.$id_direccion;
//		}	
//		if (!empty($tipo)) {
//			$goHome.='&tipo='.$tipo;
//			$godossiers.='&tipo='.$tipo;
//		}	
//		if (!empty($sin_dir)) {
//			$goHome.='&sin_dir='.$sin_dir;
//			$godossiers.='&sin_dir='.$sin_dir;
//		}	
		break;
	case 'a':
		$oActividad  = new actividades\Actividad($id_pau);
		$nom_cabecera = $oActividad->getNom_activ();
		
		$sQuery = http_build_query(array('id_activ'=>$id_pau,'obj_pau'=>$Qobj_pau));
		$goHome = Hash::link(ConfigGlobal::getWeb()."/apps/actividades/controller/actividad_ver.php?$sQuery");

		// según de donde venga, debo volver al mismo sitio...
		if (!empty($_SESSION['session_go_to']['sel']['pag'])) {
			$pag = $_SESSION['session_go_to']['sel']['pag']; //=>"lista_actividades_sg.php",
			$dir = $_SESSION['session_go_to']['sel']['dir_pag']; //=>core\ConfigGlobal::$directorio."/sg",
			$dir = str_replace(ConfigGlobal::$directorio,'',$dir);
			$form_action=Hash::link(ConfigGlobal::getWeb()."$dir/$pag");
		} else {
			$form_action=Hash::link(ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select.php');
		}
		break;
}
		

$alt=_("ver dossiers");
$dos=_("dossiers");
$titulo="<span class=link onclick=fnjs_update_div('#main','$goHome')>$nom_cabecera</span>";

// -----------------------------  cabecera ---------------------------------

echo $oPosicion->mostrar_left_slide(1);
?>
<div id="top">
<table><tr>
<td><span class="link" onclick="fnjs_update_div('#main','<?= $godossiers ?>')" ><img src=<?= ConfigGlobal::getWeb_icons() ?>/dossiers.gif border=0 width=40 height=40 alt='<?= $alt ?>'>(<?= $dos ?>)</span></td>
<td class="titulo"><?= $titulo ?></td>
</table>
</div>
<?php
//if (!empty($accion)) {
//	cerrar_dossier($pau,$id_pau,$id_tipo_dossier,$oDB);
//	$go_to="dossiers_lista.php?pau=$pau&id_pau=$id_pau&obj_pau=".$Qobj_pau."";
//	ir_a($go_to);
//}

// ------------------------- cuerpo -----------------------------
if (empty($Qid_dossier)) { // enseña la lista de dossiers.
	echo "<div id=\"ficha\">";
	include ("lista_dossiers.php");
	echo "</div>";
} else {
	// Voy a intentar mostrar dossiers seguidos. Se supone que id_dossier es una lista de nº separados por 'y'
	$id_dossier=strtok($Qid_dossier,"y");
	while  ($id_dossier) {
		// nombre del id div actual
		$nom_bloque = 'ficha'.$id_dossier;
		$bloque = '#ficha'.$id_dossier;
		echo "<div id=\"$nom_bloque\">";
		$oTipoDossier = new dossiers\TipoDossier($id_dossier);
		$tabla_dossier=$oTipoDossier->getTabla_to();
		$app=$oTipoDossier->getApp();

		// para el botón editar en la presentación general...
		if ($Qpermiso==3) { $edit=1; }
		
		// Para presentaciones particulares
		$nameFile = "../../$app/model/select".$id_dossier.".class.php";
		if (realpath($nameFile)){ //como file_exists
			$nameClaseSelect = "$app\\model\\Select".$id_dossier;
			$claseSelect = new $nameClaseSelect();
			$claseSelect->setId_dossier($id_dossier);
			$claseSelect->setPau($pau);
			$claseSelect->setObj_pau($Qobj_pau);
			$claseSelect->setId_pau($id_pau);
			$claseSelect->setPermiso($Qpermiso);
			$claseSelect->setBloque($bloque);
			$claseSelect->setQueSel($QqueSel);

			// sólo si vengo de vuelta, sino el scroll corresponde a la grid 
			// de la selección que me trae aqui.
			if (isset($_POST['stack']) && $stack != '') {
				$claseSelect->setQId_sel($Qid_sel);
				$claseSelect->setQScroll_id($Qscroll_id);
			}
			
			switch ($id_dossier) {
				case 1301:
				case 1302:
					// propio del 1302	
					$Qmodo_curso = (integer) \filter_input(INPUT_POST,'modo_curso');
					$claseSelect->setModo_curso($Qmodo_curso);
				break;
				case 1303:
					// propio del 1303	
					if (!empty($Qid_activ)) {
						$claseSelect->setQId_activ($Qid_activ);
					}
				break;
			}
			echo $claseSelect->getHtml();
		} else {
			// para presentacion genérica, con la info tipo info1012.class.php
			// datos del dossier:
			$oTipoDossier = new dossiers\TipoDossier($id_dossier);
			$app=$oTipoDossier->getApp();
			// No sé porque no acepa aqui el '_' en el nombre de la clase.
			$clase_info = "$app\\model\\info$id_dossier";
			// Tiene que ser en dos pasos.
			$obj = $clase_info;
			$oInfoClase = new $obj();
			$oInfoClase->setId_pau($id_pau);
			$oInfoClase->setObj_pau($Qobj_pau);
			
			$oDatosTabla = new core\DatosTabla();
			$oDatosTabla->setBloque($bloque);
			$oDatosTabla->setExplicacion_txt($oInfoClase->getTxtExplicacion());
			$oDatosTabla->setEliminar_txt($oInfoClase->getTxtEliminar());
			$oDatosTabla->setColeccion($oInfoClase->getColeccion());
			$oDatosTabla->setId_sel($Qid_sel);
			$oDatosTabla->setScroll_id($Qscroll_id);

			$aQuery = array(
					'clase_info' => $Qclase_info,
					'id_pau' => $id_pau,
					'bloque' => $bloque,
					'permiso' => $Qpermiso,
					);
			$aQuery['obj_pau'] = $Qobj_pau;
			$sQuery = http_build_query($aQuery);
			$Qgo_to=web\Hash::link(core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?$sQuery");
			$oDatosTabla->setAction_tabla($Qgo_to);
			
			$oHashSelect = new web\Hash();
			$oHashSelect->setCamposForm('mod');
			$oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
			$a_camposHidden = array(
					'clase_info' => $clase_info,
					'pau' => $pau,
					'id_pau' => $id_pau, // Hace falta para el boton nuevo
					'obj_pau' => $Qobj_pau,
					'permiso' => $Qpermiso,
					'bloque' => $bloque,
					);
			$oHashSelect->setArraycamposHidden($a_camposHidden);
		
			$html = '';
			$html .= '<script>';
			$html .= $oDatosTabla->getScript();
			$html .= '</script>';
			$html .= "<h3 class=subtitulo>".$oInfoClase->getTxtTitulo()."</h3>
				<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>";
			$html .= $oHashSelect->getCamposHtml();
			$html .= "<input type='hidden' id='mod' name='mod' value=''>";
			
			$oTabla = new web\Lista();
			$oTabla->setId_tabla('datos_sql'.  $id_dossier);
			$oTabla->setCabeceras($oDatosTabla->getCabeceras());
			$oTabla->setBotones($oDatosTabla->getBotones());
			$oTabla->setDatos($oDatosTabla->getValores());
			
			if (!empty($oDatosTabla->getValores())) {
				$html .= $oTabla->mostrar_tabla();
			}
			
			// Poner o no el botón de inserta. En algunos casos ya está en la presentación particular.
			if ($Qpermiso == 3) {
				$html .= "<br><table cellspacing=3  class=botones><tr class=botones>
					<td class=botones><input name=\"btn_new\" type=\"button\" value=\"";
				$html .= _("nuevo");
				// caso especial para traslados:
				if ($id_dossier == 1004) {
					$insert=Hash::link(ConfigGlobal::getWeb().'/apps/personas/controller/traslado_form.php?'.http_build_query(array('cabecera'=>'no','id_pau'=>$id_pau,'id_dossier'=>$id_dossier,'obj_pau'=>$Qobj_pau)));
					$html .= "\" onclick=\"fnjs_update_div('#main','$insert');\"></td></tr></table>";
				} else {
					$html .= "\" onclick=\"fnjs_nuevo('#seleccionados');\"></td></tr></table>";
				}
			}
			echo $html;
		} 
		echo "</div>";
		$id_dossier=strtok("y");
	}
}