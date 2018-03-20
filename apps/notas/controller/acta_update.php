<?php
use notas\model as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = core\ConfigGlobal::mi_dele();
$mi_dele .= (core\ConfigGlobal::mi_sfsv() == 2)? 'f' : '';
$acta = empty($_POST['acta'])? '' : $_POST['acta'];
$dl_acta = strtok($acta,' ');

if ($dl_acta == $mi_dele || $dl_acta == "?") {
	$oActa = new notas\ActaDl();
	$oActaTribunal = new notas\ActaTribunalDl();
} else {
	// Ojo si la dl ya existe no deberia hacerse
	$oActa = new notas\ActaEx();
}

if (!empty($_POST['nuevo'])) { // nueva.
	// Si se pone un acta ya existente, modificará los datos de ésta. Hay que avisar:
	$oActa->setActa($acta);
	if (!empty($oActa->getF_acta())) { exit(_("Esta acta ya existe")); }
	isset($_POST['id_asignatura']) ? $oActa->setId_asignatura($_POST['id_asignatura']) : '';
	isset($_POST['id_activ'])? $oActa->setId_activ($_POST['id_activ']) : '';
	// la fecha debe ir antes que el acta por si hay que inventar el acta, tener la referencia de la fecha
	isset($_POST['f_acta'])? $oActa->setF_acta($_POST['f_acta']) : '';
	// comprobar valor del acta
	if (isset($acta)) {
		$valor = trim($acta);
		$reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
		if (preg_match ($reg_exp, $valor) == 1) {
		} else {
			// inventar acta.
			$valor = $oActa->inventarActa($valor,$_POST['f_acta']);
		}
		$oActa->setActa($valor);
	}
	isset($_POST['libro'])? $oActa->setLibro($_POST['libro']) : '';
	isset($_POST['pagina'])? $oActa->setPagina($_POST['pagina']) : '';
	isset($_POST['linea'])? $oActa->setLinea($_POST['linea']) : '';
	isset($_POST['lugar'])? $oActa->setLugar($_POST['lugar']) : '';
	isset($_POST['observ'])? $oActa->setObserv($_POST['observ']) : '';
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
} else { // editar.
	$oActa->setActa($acta);
	$oActa->DBCarregar();
	isset($_POST['id_asignatura'])? $oActa->setId_asignatura($_POST['id_asignatura']) : '';
	isset($_POST['id_activ'])? $oActa->setId_activ($_POST['id_activ']) : '';
	isset($_POST['f_acta'])? $oActa->setF_acta($_POST['f_acta']) : '';
	isset($_POST['libro'])? $oActa->setLibro($_POST['libro']) : '';
	isset($_POST['pagina'])? $oActa->setPagina($_POST['pagina']) : '';
	isset($_POST['linea'])? $oActa->setLinea($_POST['linea']) : '';
	isset($_POST['lugar'])? $oActa->setLugar($_POST['lugar']) : '';
	isset($_POST['observ'])? $oActa->setObserv($_POST['observ']) : '';
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
}
//para actualizar los examinadores.
//print_r($_POST['examinador']);

//borrar todos (y despues poner los nuevos)
$oGesActaTribunal = new notas\GestorActaTribunalDl();
$cActaTribunal = $oGesActaTribunal->getActasTribunales(array('acta'=>$acta));
foreach ($cActaTribunal as $oActaTribunal) {
	if ($oActaTribunal->DBEliminar() === false) {
		echo _('Hay un error, no se ha eliminado');
	}
}

if (!empty($_POST['examinadores'])) {
    $examinadores = explode("#",$_POST['examinadores']);
	$i = 0;
    foreach($examinadores as $examinador){
		$i++;
		$oActaTribunal = new notas\ActaTribunalDl();
		$oActaTribunal->setActa($acta);
		$oActaTribunal->setExaminador($examinador);
		$oActaTribunal->setOrden($i);
		if ($oActaTribunal->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
    }
}

$go_to = empty($_POST['go_to'])? "" : $_POST['go_to'];
if (strpos($go_to,"acta_notas") !== false) {
	// No voy a ningun sitio
} else {
//vuelve a la presentacion de la ficha.
//AHORA por javascript
//echo "gou: $go_to<br>";
//$oPosicion = new web\Posicion();
//$oPosicion->setId_div('ir_a');
//echo $oPosicion->ir_a($go_to);
}
?>
