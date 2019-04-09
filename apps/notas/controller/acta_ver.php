<?php

use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use notas\model\entity as notas;
use personas\model\entity as personas;
use web\Hash;
use web\Posicion;
/**
* Esta página muestra un formulario para modificar los datos de un acta.
*
*
*@package	delegacion
*@subpackage	est
*@author	Daniel Serrabou
*@since		14/10/03.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$f_acta = '';
$libro = '';
$pagina = '';
$linea = '';
$lugar = '';
$observ = '';

// Si notas=(nuevo|acta), quiere decir que estoy en un include de actividadestudios/controller/acta_notas
$notas = empty($notas)? '': $notas;
$permiso = empty($permiso)? 3: $permiso;

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$Qmod = (string) \filter_input(INPUT_POST, 'mod');

$Qsa_actas = (string) \filter_input(INPUT_POST, 'sa_actas');
$Qa_actas = unserialize( base64_decode( $Qsa_actas ));
$Qacta = (string) \filter_input(INPUT_POST, 'acta');
$Qnotas = (string) \filter_input(INPUT_POST, 'notas');
		
if (empty($notas) && empty($Qnotas)) {
	echo $oPosicion->recordar();
}

//$acta=urldecode($acta);
//últimos
$any = date('y');
$mi_dele = core\ConfigGlobal::mi_dele();
$dl = ($mi_dele=='cr')? core\ConfigGlobal::mi_region() : $mi_dele;

$GesActas = new notas\GestorActa();
$ult_lib = $GesActas->getUltimoLibro();
$ult_pag = $GesActas->getUltimaPagina($ult_lib);
$ult_lin = $GesActas->getUltimaLinea($ult_lib);
$ult_acta = $GesActas->getUltimaActa($dl,$any);
$acta_new = '';
	
$obj = 'notas\\model\\entity\\ActaDl';

//Distingo la procedencia.
if (empty($notas) && empty($Qnotas)) {
	// No estoy dentro de la pagina de acta_notas
	if (!empty($a_sel)) {
		//vengo de un checkbox y no estoy en la página de acta_notas ($notas).
		$acta_actual=urldecode(strtok($a_sel[0],"#"));
	} else {
		// si vengo por un link en el nombre del acta, sólo tengo el acta encoded
		$acta_actual = urldecode($Qacta);
	}
	$a_actas = array($acta_actual);
} else { 
	// Dentro de la página acta_notas.
	if (isset($cActas) && is_array($cActas)) {
		$a_actas = [];
		foreach ($cActas as $oActa) {
			$a_actas[] = $oActa->getActa();
		}
		//por defecto la primera
		$acta_actual = empty($a_actas[0])? '' : $a_actas[0];
	} elseif (!empty ($Qa_actas)) {  // Estoy en la pagina notas y cambio el div de actas
		$a_actas = $Qa_actas;
		$acta_actual = $Qacta;
		$notas = $Qnotas;
	}
}

$json_examinadores = '';
if ($notas != 'nuevo' && $Qmod != 'nueva' && !empty($acta_actual))  { //significa que no es nuevo
	if (false && !empty($Qacta) && !empty($notas)) { // vengo de actualizar esta pág.
		// estoy actualizando la página
		$id_asignatura_actual = (integer) \filter_input(INPUT_POST, 'id_asignatura_actual');
		$id_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
		$f_acta = (string) \filter_input(INPUT_POST, 'f_acta');
		$libro = (string) \filter_input(INPUT_POST, 'libro');
		$pagina = (integer) \filter_input(INPUT_POST, 'pagina');
		$linea = (integer) \filter_input(INPUT_POST, 'linea');
		$lugar = (string) \filter_input(INPUT_POST, 'lugar');
		$observ = (string) \filter_input(INPUT_POST, 'observ');
		$permiso = (integer) \filter_input(INPUT_POST, 'permiso');
	} else {
		$oActa = new notas\Acta($acta_actual);
		$id_asignatura = $oActa->getId_asignatura();
		$id_activ = $oActa->getId_activ();
		$f_acta = $oActa->getF_acta()->getFromLocal();
		$libro = $oActa->getLibro();
		$pagina = $oActa->getPagina();
		$linea = $oActa->getLinea();
		$lugar = $oActa->getLugar();
		$observ =$oActa->getObserv();
		$id_asignatura_actual=$id_asignatura;
	}
} else {
	//busco la última acta (para ayudar)
	//
	//echo "aa: $query_acta<br>";
	$num_acta = $ult_acta + 1;
	$ult_acta= "$dl {$ult_acta}/{$any}";
	$acta_new= "$dl {$num_acta}/{$any}";
	
	if ($notas=="nuevo") { //vengo de un ca
		$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
		$id_activ = empty($id_activ)? $Qid_activ : $id_activ;
		$Qid_asignatura = (string) \filter_input(INPUT_POST, 'id_asignatura');
		$id_asignatura_actual = empty($id_asignatura)? $Qid_asignatura : $id_asignatura;
		// Busco al profesor como examinador principal.
		$oActividadAsignatura= new actividadestudios\ActividadAsignaturaDl();
		$oActividadAsignatura->setId_activ($id_activ);
		$oActividadAsignatura->setId_asignatura($id_asignatura_actual);
		$oActividadAsignatura->DBCarregar();
		$id_profesor=$oActividadAsignatura->getId_profesor();
		$oPersonaDl = new personas\PersonaDl($id_profesor);
		$ap_nom = $oPersonaDl->getTituloNombreLatin();
		$examinador = $ap_nom;
		$json_examinadores = 'prePopulate: [';
		$json_examinadores .= '{name: "'.htmlspecialchars($examinador).'"}';
		$json_examinadores .= '],';
	} else { // estoy actualizando la página
		if (!empty($a_sel) && !empty($notas)) { //vengo de un checkbox y estoy en la página de acta_notas ($notas).
		    $id_activ = (integer) strtok($a_sel[0],'#');
		    $id_asignatura = (integer) strtok('#');
			$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			$oActa = $cActas[0];
			$id_asignatura = $oActa->getId_asignatura();
			$id_activ = $oActa->getId_activ();
			$f_acta = $oActa->getF_acta()->getFromLocal();
			$libro = $oActa->getLibro();
			$pagina = $oActa->getPagina();
			$linea = $oActa->getLinea();
			$lugar = $oActa->getLugar();
			$observ =$oActa->getObserv();
			$id_asignatura_actual=$id_asignatura;
		} else {
			$id_asignatura_actual='';
		}
	}
}

if (!empty($ult_lib)) { $ult_lib=sprintf(_("(último= %s)"),$ult_lib); }
if (!empty($ult_pag)) { $ult_pag=sprintf(_("(última= %s)"),$ult_pag); }
if (!empty($ult_lin)) { $ult_lin=sprintf(_("(última= %s)"),$ult_lin); }
if (!empty($ult_acta)) { $ult_acta=sprintf(_("(última= %s)"),$ult_acta); }

if (!empty($acta_actual)) {
	$GesTribunal = new notas\GestorActaTribunalDl();
	$cTribunal = $GesTribunal->getActasTribunales(array('acta'=>$acta_actual,'_ordre'=>'orden')); 
} else {
	$cTribunal = array();
}

$GesAsignaturas = new asignaturas\GestorAsignatura();
$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas();
if (!empty($id_asignatura_actual)) {
	$jsonTodas = $GesAsignaturas->getJsonAsignaturas(array('id'=>$id_asignatura_actual));
	$json_asignaturas = 'prePopulate: '.$jsonTodas.',';
} else {
	$json_asignaturas = '';
}

$oHashActa = new Hash();
$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!f_acta!acta';
if ($Qmod == 'nueva' || $notas=="nuevo") { 
	$sCamposForm .= '!acta';
	$sCamposForm .= '!f_acta';
}
if(!empty($cTribunal)) {
	//$sCamposForm .= '!item';
	$sCamposForm .= '!examinadores';
}
$oHashActa->setcamposForm($sCamposForm);
$oHashActa->setCamposNo('go_to!examinadores!notas!refresh');
$a_camposHidden = array();
if ($Qmod == 'nueva' || $notas=="nuevo") { 
	$a_camposHidden['mod'] = 'nueva';
	if (empty($id_activ)) {
		echo _("no se guardará el ca/cv donde se cursó la asignatura");
	} else {
		$a_camposHidden['id_activ'] = $id_activ;
	}
} else {
//	$a_camposHidden['acta'] = $acta;
	$a_camposHidden['mod'] = '';
	$a_camposHidden['id_activ'] = $id_activ;
	$a_camposHidden['sa_actas'] = \base64_encode( serialize($a_actas));
	$a_camposHidden['notas'] = $notas;
}
$oHashActa->setArraycamposHidden($a_camposHidden);

$titulo=strtoupper(_("datos del acta"));

$e = 0;
if (!empty($cTribunal)) { 
	$json_examinadores = 'prePopulate: [';
	foreach ($cTribunal as $oActaTribunal) {
		$id_item=$oActaTribunal->getId_item();
		$examinador=$oActaTribunal->getExaminador();
		$orden=$oActaTribunal->getOrden();
		$json_examinadores .= ($e > 0)? ',' : '';
		$json_examinadores .= '{name: "'.htmlspecialchars($examinador).'"}';
		$e++;
	}
	$json_examinadores .= '],';
}

$url = ConfigGlobal::getWeb().'/apps/notas/controller/acta_ajax.php';
$oHashLink = new Hash();
$oHashLink->setUrl($url);
$oHashLink->setCamposForm('que!q'); 
$h = $oHashLink->linkSinVal();

$location = "{$url}?que=examinadores&$h";
$loc_asig = "{$url}?que=asignaturas&$h";


$a_campos = ['obj' => $obj,
			'oPosicion' => $oPosicion,
			'notas' => $notas,
			'mod' => $Qmod,
			'oHashActa' => $oHashActa,
			'titulo' => $titulo,
			'acta_actual' => $acta_actual,
			'acta_new' => $acta_new,
			'ult_acta' => $ult_acta,
			'f_acta' => $f_acta,
			'libro' => $libro,
			'ult_lib' => $ult_lib,
			'pagina' => $pagina,
			'ult_pag' => $ult_pag,
			'linea' => $linea,
			'ult_lin' => $ult_lin,
			'lugar' => $lugar,
			'observ' => $observ,
			'location' => $location,
			'loc_asig' => $loc_asig,
			'json_asignaturas' => $json_asignaturas,
			'json_examinadores' => $json_examinadores,
			'a_actas' => $a_actas,
			'permiso' => $permiso,
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_ver.phtml',$a_campos);