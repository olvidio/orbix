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

if (empty($notas)) {
	echo $oPosicion->recordar();
}

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$Qacta = (string) \filter_input(INPUT_POST, 'acta');
$Qmod = (string) \filter_input(INPUT_POST, 'mod');

//$acta=urldecode($acta);
//últimos
$any = date('y');
$dl = core\ConfigGlobal::mi_dele();

$GesActas = new notas\GestorActa();
$ult_lib = $GesActas->getUltimoLibro();
$ult_pag = $GesActas->getUltimaPagina($ult_lib);
$ult_lin = $GesActas->getUltimaLinea($ult_lib);
$ult_acta = $GesActas->getUltimaActa($dl,$any);

$obj = 'notas\\model\\entity\\ActaDl';

if (!empty($a_sel) && empty($notas)) { //vengo de un checkbox y no estoy en la página de acta_notas ($notas).
	$notas = '';
	$acta=urldecode(strtok($a_sel[0],"#"));
} else { // vengo de un link 
	if (empty($acta) && !empty($Qacta)) $acta=urldecode($Qacta); // si estoy  en la página de acta_notas ya tengo el acta.
}

$json_examinadores = '';
if ($Qmod != 'nueva' && !empty($acta))  { //significa que no es nuevo
	if (!empty($Qacta) && !empty($notas)) { // vengo de actualizar esta pág.
		// estoy actualizando la página
		$id_asignatura_actual = (integer) \filter_input(INPUT_POST, 'id_asignatura_actual');
		$id_actividad = (integer) \filter_input(INPUT_POST, 'id_actividad');
		$f_acta = (string) \filter_input(INPUT_POST, 'f_acta');
		$libro = (string) \filter_input(INPUT_POST, 'libro');
		$pagina = (integer) \filter_input(INPUT_POST, 'pagina');
		$linea = (integer) \filter_input(INPUT_POST, 'linea');
		$lugar = (string) \filter_input(INPUT_POST, 'lugar');
		$observ = (string) \filter_input(INPUT_POST, 'observ');
	} else {
		$oActa = new notas\Acta($acta);
		$id_asignatura = $oActa->getId_asignatura();
		$id_activ = $oActa->getId_activ();
		$f_acta = $oActa->getF_acta();
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
	$acta= "$dl {$num_acta}/{$any}";
	
	if ($notas=="nuevo") { //vengo de un ca
		$id_asignatura_actual=$id_asignatura;
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
			$id_activ = strtok($a_sel[0],'#');
			$id_asignatura = strtok('#');
			$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			$oActa = $cActas[0];
			$id_asignatura = $oActa->getId_asignatura();
			$id_activ = $oActa->getId_activ();
			$f_acta = $oActa->getF_acta();
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

if (!empty($acta)) {
	$GesTribunal = new notas\GestorActaTribunalDl();
	$cTribunal = $GesTribunal->getActasTribunales(array('acta'=>$acta,'_ordre'=>'orden')); 
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
$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!f_acta';
if ($Qmod == 'nueva' || $notas=="nuevo") { 
	$sCamposForm .= '!acta';
	$sCamposForm .= '!f_acta';
}
if(!empty($cTribunal)) {
	//$sCamposForm .= '!item';
	$sCamposForm .= '!examinadores';
}
$oHashActa->setcamposForm($sCamposForm);
$oHashActa->setCamposNo('go_to!examinadores');
$a_camposHidden = array();
if ($Qmod == 'nueva' || $notas=="nuevo") { 
	$a_camposHidden['mod'] = 'nueva';
	if (empty($id_activ)) {
		echo _('No se guardará el ca/cv donde se cursó la asignatura');
	} else {
		$a_camposHidden['id_activ'] = $id_activ;
	}
} else {
	$a_camposHidden['acta'] = $acta;
	$a_camposHidden['id_activ'] = $id_activ;
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
			'acta' => $acta,
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
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_ver.phtml',$a_campos);