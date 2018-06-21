<?php
/**
 * Muestra un formulario para introducir/cambiar los datos de la asistencia
 * de una persona a una actividad
 * 
 *
 * @package	orbix
 * @subpackage	asistentes
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 * @param string $_POST['pau']  para el controlador dossiers_ver
 * @param integer $_POST['id_pau']  para el controlador dossiers_ver
 * @param string $_POST['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param string $_POST['mod_curso']  para mantener la selección del curso
 * @param integer $_POST['permiso'] valores 1, 2, 3
 * @param integer $_POST['scroll_id'] 
 * @param array $_POST['sel'] con id_activ#id_asignatura
 * En el caso de nuevo:
 * @param string $_POST['que_dl'] la propia dl o vacio para otras
 * @param integer $_POST['id_tipo'] selección del tipo de actividad
 * 
 */

use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qpermiso = (string) \filter_input(INPUT_POST,'permiso');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$Qid_nom = strtok($a_sel[0],"#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
}

$Qid_activ = (integer)  \filter_input(INPUT_POST, 'id_pau');
$Qid_pau = (integer)  \filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)  \filter_input(INPUT_POST, 'obj_pau');

$gesAsistentes = new asistentes\GestorAsistente();
	
$obj = 'asistentes\\model\\entity\\Asistente';

/* Mirar si la actividad es mia o no */
$oActividad = new actividades\Actividad($Qid_activ);
// si es de la sf quito la 'f'
$dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
$id_tabla_dl = $oActividad->getId_tabla();

if (!empty($Qid_nom)) { //caso de modificar
	$mod="editar";
	$oPersona = personas\Persona::NewPersona($Qid_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $Qid_nom en  ".__FILE__.": line ". __LINE__;
		exit($msg_err);
	}
	$ape_nom = $oPersona->getApellidosNombre();
	$id_tabla = $oPersona->getId_tabla();
	$id_nom_real = $Qid_nom;

	$aWhere = array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom);
	$cAsistentes = $gesAsistentes->getAsistentes($aWhere);
	$oAsistente = $cAsistentes[0];

	$obj_pau = str_replace("personas\\model\\entity\\",'',get_class($oPersona));
	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();
	$plaza=$oAsistente->getPlaza();
	$propietario=$oAsistente->getPropietario();
	
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		if (!empty($propietario)) {
			$padre = strtok($propietario,'>');
			$child = strtok('>');
			//$obj_asis = str_replace("personas\\model\\entity\\",'',get_class($oAsistente));
			//if ($obj_asis == 'AsistenteOut' && $padre != core\ConfigGlobal::mi_dele() ) {
			// excepto los de paso
			if ( $obj_pau != 'PersonaEx' && $child != core\ConfigGlobal::mi_dele() ) {
				exit (sprintf(_("Los datos de asistencia los modifica el propietario de la plaza: %s"),$child));
			}
		}
	}
	$oDesplegablePersonas = array();
} else { //caso de nuevo asistente
	$mod="nuevo";
	$id_nom_real = '';
	$ape_nom = '';
	$propio="t"; //valor por defecto
	$observ=""; //valor por defecto
	$plaza=  asistentes\Asistente::PLAZA_PEDIDA; //valor por defecto
	$propietario=''; //valor por defecto
	$Qobj_pau = !empty($Qobj_pau)? urldecode($Qobj_pau) : '';
	$obj_pau = strtok($Qobj_pau,'&');
	$na = strtok('&');
	$na_txt = strtok($na,'=');
	$na_val = 'p'.strtok('=');
	switch ($obj_pau) {
		case 'PersonaN':
			$oPersonas=new personas\GestorPersonaN();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaNax':
			$oPersonas=new personas\GestorPersonaNax();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaAgd':
			$oPersonas=new personas\GestorPersonaAgd();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaS':
			$oPersonas=new personas\GestorPersonaS();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaSSSC':
			$oPersonas=new personas\GestorPersonaSSSC();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaEx':
			$oPersonas=new personas\GestorPersonaEx();
			$oDesplegablePersonas = $oPersonas->getListaPersonas($na_val);
			$oDesplegablePersonas->setNombre('id_nom');
			$obj_pau = 'PersonaEx';
			break;
	}
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		$oDesplegablePersonas->setAction('fnjs_cmb_propietario()');
	}
}
$propio_chk = (!empty($propio) && $propio=='t') ? 'checked' : '' ;
$falta_chk = (!empty($falta) && $falta=='t') ? 'checked' : '' ;
$est_chk = (!empty($est_ok) && $est_ok=='t') ? 'checked' : '' ;

if (core\configGlobal::is_app_installed('actividadplazas')) {
	$oDesplegablePlaza = $gesAsistentes->getPosiblesPlaza();
	$oDesplegablePlaza->setNombre('plaza');
	$oDesplegablePlaza->setOpcion_sel($plaza);
	
	$dl_de_paso = FALSE;
	if ($obj_pau === 'PersonaEx') {
		if (!empty($id_nom)) { //caso de modificar
			$dl_de_paso = $oPersona->getDl();
		} else {
		
		}
	}
	$gesActividadPlazas = new \actividadplazas\model\GestorResumenPlazas();
	$gesActividadPlazas->setId_activ($Qid_activ);
	$oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
	$oDesplPosiblesPropietarios->setNombre('propietario');
	$oDesplPosiblesPropietarios->setOpcion_sel($propietario);

	$url_ajax = core\ConfigGlobal::getWeb().'/apps/actividadplazas/controller/gestion_plazas_ajax.php';
	$oHash1 = new web\Hash();
	$oHash1->setUrl($url_ajax);
	$oHash1->setCamposForm('que!id_activ!id_nom'); 
	//$oHash1->setCamposNo('id_nom'); 
	$h1 = $oHash1->linkSinVal();
}

$oHash = new web\Hash();
$camposForm = 'observ';
if (core\configGlobal::is_app_installed('actividadplazas')) {
	$camposForm .= '!plaza!propietario';
}
$oHash->setCamposNo('propio!falta!est_ok');
$a_camposHidden = array(
		'id_activ' => $Qid_pau,
		'obj_pau'=> $obj_pau,
		'mod' => $mod,
		);
if (!empty($id_nom_real)) {
	$a_camposHidden['id_nom'] = $id_nom_real;
} else {
	$camposForm .= '!id_nom';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$oPosicion->addParametro('mod',$mod,0);
		
$a_campos = ['obj' => $obj,
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'h1' => $h1,
			'url_ajax' => $url_ajax,
			'id_activ' => $Qid_activ,
			'id_nom_real' => $id_nom_real,
			'ape_nom' => $ape_nom,
			'oDesplegablePersonas' => $oDesplegablePersonas,
			'propio_chk' => $propio_chk,
			'falta_chk' => $falta_chk,
			'est_chk' => $est_chk,
			'observ' => $observ,
			'oDesplegablePlaza' => $oDesplegablePlaza,
			'oDesplPosiblesPropietarios' => $oDesplPosiblesPropietarios,
			];

$oView = new core\View('asistentes/controller');
echo $oView->render('form_3101.phtml',$a_campos);