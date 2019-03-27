<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
use personas\model\entity as personas;
use web\Posicion;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$notas=1; // para indicar a la página de actas que está dentro de ésta.

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_sel = '';
$Qscroll_id = (string) \filter_input(INPUT_POST, 'scroll_id');
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
}

if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer) strtok($a_sel[0],"#");
    $id_asignatura= (integer) strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$id_asignatura = (integer) \filter_input(INPUT_POST, 'id_asignatura');
	$id_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
}

$GesNotas = new notas\GestorNota();
$oDesplNotas = $GesNotas->getListaNotas();
$oDesplNotas->setNombre('id_situacion[]');

$oActividad = new actividades\Actividad($id_activ);
$nom_activ = $oActividad->getNom_activ();

$GesMatriculas = new actividadestudios\GestorMatricula();
$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$id_asignatura, 'id_activ'=>$id_activ));
$matriculados=count($cMatriculados);
$aPersonasMatriculadas = array(); 
if ($matriculados > 0) {
	// para ordenar
	$msg_err = '';
	foreach($cMatriculados as $oMatricula) {
		$id_nom=$oMatricula->getId_nom();
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
			continue;
		}
		$nom=$oPersona->getApellidosNombre();
		$aPersonasMatriculadas[$nom] = $oMatricula;
	}
	uksort($aPersonasMatriculadas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
} else {
	echo _("no hay ninguna persona matriculada de esta asignatura");
}

$Qque = (string) \filter_input(INPUT_POST, 'que');
$permiso = (integer) \filter_input(INPUT_POST, 'permiso');
$Qid_pau = (integer) \filter_input(INPUT_POST, 'id_pau');
$Qopcional = (string) \filter_input(INPUT_POST, 'opcional');
$Qprimary_key_s = (string) \filter_input(INPUT_POST, 'primary_key_s');
$Qid_nivel = (integer) \filter_input(INPUT_POST, 'id_nivel');

$GesActas = new notas\GestorActa();
$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura, '_ordre'=>'f_acta'));
$acta_principal = '';
if (is_array($cActas) && !empty($cActas)) {
	$a_actas = [ 0 => '', 2 => _("cursada")];
	foreach ($cActas as $oActa) {
		$nom_acta=$oActa->getActa();
		$a_actas[$nom_acta]=$oActa->getActa();
	}
	$notas="acta"; // para indicar a la página de actas que está dentro de ésta.
	$oDesplActas = new web\Desplegable();
	$oDesplActas->setNombre('acta_nota[]');
	$oDesplActas->setOpciones($a_actas);
	// Si sólo hay una, la selecciono por defecto.
	if (count($cActas) == 1) {
	    $acta_principal = $nom_acta;
	}
} else {
	$notas="nuevo";// para indicar a la página de actas que está dentro de ésta.
	$oDesplActas = new web\Desplegable();
	$oDesplActas->setOpciones(array('primero gurardar acta'));
}

$oHashNotas = new web\Hash();
$oHashNotas->setcamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$a_camposHidden1 = array(
		'id_pau' => $Qid_pau,
		'id_activ' => $id_activ,
		'opcional' => $Qopcional,
		'primary_key_s' => $Qprimary_key_s,
		'id_asignatura' => $id_asignatura,
		'id_nivel' => $Qid_nivel,
		'matriculados' => $matriculados
		);
$oHashNotas->setArraycamposHidden($a_camposHidden1);

if (!empty($msg_err)) { echo $msg_err; }

$txt_alert_acta = _("primero debe guadar los datos del acta");

// El formulario del acta:
include_once ("apps/notas/controller/acta_ver.php"); 

$a_campos = ['oPosicion' => $oPosicion,
			'oHashNotas' => $oHashNotas,
			'permiso' => $permiso,
			'Qque' => $Qque,
			'aPersonasMatriculadas' => $aPersonasMatriculadas,
			'oDesplActas' => $oDesplActas,
    	    'acta_principal'  => $acta_principal,
			'txt_alert_acta' => $txt_alert_acta,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('acta_notas.phtml',$a_campos);
