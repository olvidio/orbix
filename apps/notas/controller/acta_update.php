<?php
use notas\model\entity as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = core\ConfigGlobal::mi_dele();
$mi_dele .= (core\ConfigGlobal::mi_sfsv() == 2)? 'f' : '';

$Qacta = (string) \filter_input(INPUT_POST, 'acta');
$dl_acta = strtok($Qacta,' ');
$Qnuevo = (string) \filter_input(INPUT_POST, 'nuevo');

if ($dl_acta == $mi_dele || $dl_acta == "?") {
	$oActa = new notas\ActaDl();
	$oActaTribunal = new notas\ActaTribunalDl();
} else {
	// Ojo si la dl ya existe no deberia hacerse
	$oActa = new notas\ActaEx();
}

$Qid_asignatura = (integer) \filter_input(INPUT_POST, 'id_asignatura');
$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
$Qf_acta = (string) \filter_input(INPUT_POST, 'f_acta');
$Qlibro = (integer) \filter_input(INPUT_POST, 'libro');
$Qpagina = (integer) \filter_input(INPUT_POST, 'pagina');
$Qlinea = (integer) \filter_input(INPUT_POST, 'linea');
$Qlugar = (string) \filter_input(INPUT_POST, 'lugar');
$Qobserv = (string) \filter_input(INPUT_POST, 'observ');

if (!empty($Qnuevo)) { // nueva.
	// Si se pone un acta ya existente, modificará los datos de ésta. Hay que avisar:
	$oActa->setActa($Qacta);
	if (!empty($oActa->getF_acta())) { exit(_("Esta acta ya existe")); }
	
	$oActa->setId_asignatura($Qid_asignatura);
	$oActa->setId_activ($Qid_activ);
	// la fecha debe ir antes que el acta por si hay que inventar el acta, tener la referencia de la fecha
	$oActa->setF_acta($Qf_acta);
	// comprobar valor del acta
	if (isset($Qacta)) {
		$valor = trim($Qacta);
		$reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
		if (preg_match ($reg_exp, $valor) == 1) {
		} else {
			// inventar acta.
			$valor = $oActa->inventarActa($valor,$Qf_acta);
		}
		$oActa->setActa($valor);
	}
	$oActa->setLibro($Qlibro);
	$oActa->setPagina($Qpagina);
	$oActa->setLinea($Qlinea);
	$oActa->setLugar($Qlugar);
	$oActa->setObserv($Qobserv);
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
} else { // editar.
	$Qid_asignatura = (integer) \filter_input(INPUT_POST, 'id_asignatura');
	$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
	$Qf_acta = (string) \filter_input(INPUT_POST, 'f_acta');
	$Qlibro = (integer) \filter_input(INPUT_POST, 'libro');
	$Qpagina = (integer) \filter_input(INPUT_POST, 'pagina');
	$Qlinea = (integer) \filter_input(INPUT_POST, 'linea');
	$Qlugar = (string) \filter_input(INPUT_POST, 'lugar');
	$Qobserv = (string) \filter_input(INPUT_POST, 'observ');

	$oActa->setActa($Qacta);
	$oActa->DBCarregar();

	$oActa->setId_asignatura($Qid_asignatura);
//	$oActa->setId_activ($Qid_activ);
	$oActa->setF_acta($Qf_acta);
	$oActa->setLibro($Qlibro);
	$oActa->setPagina($Qpagina);
	$oActa->setLinea($Qlinea);
	$oActa->setLugar($Qlugar);
	$oActa->setObserv($Qobserv);
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
}

//borrar todos (y despues poner los nuevos)
$oGesActaTribunal = new notas\GestorActaTribunalDl();
$cActaTribunal = $oGesActaTribunal->getActasTribunales(array('acta'=>$Qacta));
foreach ($cActaTribunal as $oActaTribunal) {
	if ($oActaTribunal->DBEliminar() === false) {
		echo _('Hay un error, no se ha eliminado');
	}
}

$Qexaminadores = (string) \filter_input(INPUT_POST, 'examinadores');
if (!empty($Qexaminadores)) {
    $examinadores = explode("#",$Qexaminadores);
	$i = 0;
    foreach($examinadores as $examinador){
		$i++;
		$oActaTribunal = new notas\ActaTribunalDl();
		$oActaTribunal->setActa($Qacta);
		$oActaTribunal->setExaminador($examinador);
		$oActaTribunal->setOrden($i);
		if ($oActaTribunal->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
    }
}