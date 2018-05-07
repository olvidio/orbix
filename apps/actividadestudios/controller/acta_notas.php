<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use notas\model\entity as notas;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$notas=1; // para indicar a la página de actas que está dentro de ésta.

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_activ = strtok($a_sel[0],"#");
	$id_asignatura=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
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
	echo _("No hay ninguna persona matriculada de esta asignatura");
}

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_pau = (integer) \filter_input(INPUT_POST, 'id_pau');
$Qopcional = (string) \filter_input(INPUT_POST, 'opcional');
$Qprimary_key_s = (string) \filter_input(INPUT_POST, 'primary_key_s');
$Qid_nivel = (integer) \filter_input(INPUT_POST, 'id_nivel');

$GesActas = new notas\GestorActa();
$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
if (is_array($cActas) && count($cActas) == 1) {
	$oActa = $cActas[0];
	$acta=$oActa->getActa();
	$notas="acta"; // para indicar a la página de actas que está dentro de ésta.
} else {
	$notas="nuevo";// para indicar a la página de actas que está dentro de ésta.
}

$oHashNotas = new web\Hash();
$oHashNotas->setcamposForm('id_nom!nota_num!nota_max!form_preceptor');
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

// El formulario del acta:
include_once ("apps/notas/controller/acta_ver.php"); 

$a_campos = ['oPosicion' => $oPosicion,
			'oHashNotas' => $oHashNotas,
			'Qque' => $Qque,
			'aPersonasMatriculadas' => $aPersonasMatriculadas,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('acta_notas.phtml',$a_campos);