<?php
/**
 * Muestra un formulario para introducir/cambiar las actividades a las que
 * asiste una persona.
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
use personas\model\entity\PersonaEx;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
	
$Qid_nom = (integer)  \filter_input(INPUT_POST, 'id_pau');
$obj_pau = (string)  \filter_input(INPUT_POST, 'obj_pau');
$id_tipo = (string)  \filter_input(INPUT_POST, 'id_tipo');
$que_dl = (string)  \filter_input(INPUT_POST, 'que_dl');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer) strtok($a_sel[0],"#");
    $id_asignatura= (integer) strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$id_activ = '';
}

$oDesplActividades = array();
if (!empty($id_activ)) { //caso de modificar
	$mod="editar";
	/* Mirar si la actividad es mia o no */
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_activ));
	$nom_activ=$oActividad->getNom_activ();
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla_dl = $oActividad->getId_tabla();

	$oAsistente= asistentes\Asistente::getClaseAsistente($obj_pau, $dl, $id_tabla_dl);
	$oAsistente->setPrimary_key(array('id_activ'=>$id_activ,'id_nom'=>$Qid_nom));
	$obj = get_class($oAsistente);
	
	$id_activ_real=$id_activ;
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
				exit (sprintf(_("los datos de asistencia los modifica el propietario de la plaza: %s"),$child));
			}
		}
	}
} else { //caso de nuevo asistente
	$mod="nuevo";
	$id_activ_real = '';
	$nom_activ = '';
	if (empty($id_tipo)) {
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		$id_tipo='^'.$mi_sfsv;  //caso genérico para todas las actividades
	} else {
		$id_tipo = '^'.$id_tipo;
	}
	
	$condicion = "AND status = " . actividades\ActividadAll::STATUS_ACTUAL;
	if (!empty($que_dl)) { 
		$condicion .= " AND dl_org = '$que_dl'";
	} else {
		$condicion .= " AND dl_org != '".core\ConfigGlobal::mi_dele()."'";
	}
	
	$oGesActividades = new actividades\GestorActividad();
	$oDesplActividades = $oGesActividades->getListaActividadesDeTipo($id_tipo,$condicion);
	$oDesplActividades->setNombre('id_activ');
	
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		$oDesplActividades->setAction('fnjs_cmb_propietario()');
	}

	$propio="t"; //valor por defecto
	$falta="f"; //valor por defecto
	$est_ok="f"; //valor por defecto
	$observ=""; //valor por defecto
	$plaza=  asistentes\Asistente::PLAZA_PEDIDA; //valor por defecto
	$propietario=''; //valor por defecto
	
	// supongo que es de mi dl
	// TODO: si es otro??
	$obj = 'asistentes\\model\\entity\\AsistenteDl';
}
$propio_chk = (!empty($propio) && $propio=='t') ? 'checked' : '' ;
$falta_chk = (!empty($falta) && $falta=='t') ? 'checked' : '' ;
$est_chk = (!empty($est_ok) && $est_ok=='t') ? 'checked' : '' ;

if (core\configGlobal::is_app_installed('actividadplazas')) {
	$gesAsistentes = new asistentes\GestorAsistente();
	$oDesplegablePlaza = $gesAsistentes->getPosiblesPlaza();
	$oDesplegablePlaza->setNombre('plaza');
	$oDesplegablePlaza->setOpcion_sel($plaza);
	
	$dl_de_paso = FALSE;
	if ($obj_pau === 'PersonaEx') {
		if (!empty($Qid_nom)) { //caso de modificar
		    $oPersona = new PersonaEx(['id_nom' => $Qid_nom]);
			$dl_de_paso = $oPersona->getDl();
		} else {
		
		}
	}
	$gesActividadPlazas = new \actividadplazas\model\GestorResumenPlazas();
	if (!empty($id_activ)) {
		$gesActividadPlazas->setId_activ($id_activ);
		$oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
		$oDesplPosiblesPropietarios->setNombre('propietario');
		$oDesplPosiblesPropietarios->setOpcion_sel($propietario);
	} else {
		$oDesplPosiblesPropietarios = new web\Desplegable('propietario',array(),'');
	}

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
		'pau' => 'p',
		'id_nom' => $Qid_nom,
		'obj_pau'=> $obj_pau,
		'mod' => $mod,
		);
if (!empty($id_activ_real)) {
	$a_camposHidden['id_activ'] = $id_activ_real;
} else {
	$camposForm .= '!id_activ';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = [
			'obj' => $obj, //sirve para comprobar campos
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'h1' => $h1,
			'url_ajax' => $url_ajax,
			'id_nom' => $Qid_nom,
			'id_activ_real' => $id_activ_real,
			'nom_activ' => $nom_activ,
			'oDesplActividades' => $oDesplActividades,
			'propio_chk' => $propio_chk,
			'falta_chk' => $falta_chk,
			'est_chk' => $est_chk,
			'observ' => $observ,
			'oDesplegablePlaza' => $oDesplegablePlaza,
			'oDesplPosiblesPropietarios' => $oDesplPosiblesPropietarios,
			];

$oView = new core\View('asistentes/model');
echo $oView->render('form_1301.phtml',$a_campos);
