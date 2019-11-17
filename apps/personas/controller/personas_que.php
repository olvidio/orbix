<?php
/**
* Esta página muestra un formulario con las opciones para escoger a una persona.
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		8/2007.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}
$Qmodo = (string) \filter_input(INPUT_POST, 'modo');

$Qna = (string) \filter_input(INPUT_POST, 'na');
$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
$Qtabla = (string) \filter_input(INPUT_POST, 'tabla');
$Qes_sacd = (integer) \filter_input(INPUT_POST, 'es_sacd');

$Qexacto = (string) \filter_input(INPUT_POST, 'exacto');
$Qcmb = (string) \filter_input(INPUT_POST, 'cmb');
$Qnombre = (string) \filter_input(INPUT_POST, 'nombre');
$Qapellido1 = (string) \filter_input(INPUT_POST, 'apellido1');
$Qapellido2 = (string) \filter_input(INPUT_POST, 'apellido2');
$Qcentro = (string) \filter_input(INPUT_POST, 'centro');

if (!empty($Qtabla)) {
	$nom_tabla=substr($Qtabla,2);
	if ($nom_tabla=="de_paso") $nom_tabla=$Qna." ".$nom_tabla;
} else {
	$Qtabla="personas";
	$nom_tabla=ucfirst(_("todos"));
}
if ($Qque=="telf") {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php';
} else {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php';
}
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!exacto!cmb');
$oHash->setcamposNo('exacto!cmb');
$a_camposHidden = array(
		'tipo' => $Qtipo,
		'tabla' => $Qtabla,
		'na' => $Qna,
		'que' => $Qque,
		'es_sacd' => $Qes_sacd,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$chk_cmb = empty($Qcmb)? '' : 'checked="checked"';
$chk_exacto_0 = empty($Qexacto)? 'checked' : 0;
$chk_exacto_1 = empty($Qexacto)? '' : 'checked';


$a_campos = [
			'oHash' => $oHash,
			'action' => $action,
			'nom_tabla' => $nom_tabla,
			'chk_exacto_0' => $chk_exacto_0,
			'chk_exacto_1' => $chk_exacto_1,
			'chk_cmb' => $chk_cmb,
			'tabla' => $Qtabla,
			'tipo' => $Qtipo,
			'nombre' => $Qnombre,
			'apellido1' => $Qapellido1,
			'apellido2' => $Qapellido2,
			'centro' => $Qcentro,
			];

$oView = new core\View('personas/controller');
echo $oView->render('personas_que.phtml',$a_campos);