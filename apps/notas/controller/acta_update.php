<?php
use notas\model as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = core\ConfigGlobal::mi_dele();
$acta = empty($_POST['acta'])? '' : $_POST['acta'];
$dl_acta = strtok($acta,' ');

if ($dl_acta == $mi_dele) {
	$oActa = new notas\ActaDl();
	$oActaTribunal = new notas\ActaTribunalDl();
} else {
	// Ojo si la dl ya existe no deberia hacerse
	$oActa = new notas\ActaEx();
}
if (!empty($_POST['nuevo'])) { // nueva.
	isset($_POST['id_asignatura']) ? $oActa->setId_asignatura($_POST['id_asignatura']) : '';
	isset($_POST['id_activ']) ? $oActa->setId_activ($_POST['id_activ']) : '';
	isset($_POST['acta']) ? $oActa->setActa($_POST['acta']) : '';
	isset($_POST['f_acta']) ? $oActa->setF_acta($_POST['f_acta']) : '';
	isset($_POST['libro']) ? $oActa->setLibro($_POST['libro']) : '';
	isset($_POST['pagina']) ? $oActa->setPagina($_POST['pagina']) : '';
	isset($_POST['linea']) ? $oActa->setLinea($_POST['linea']) : '';
	isset($_POST['lugar']) ? $oActa->setLugar($_POST['lugar']) : '';
	isset($_POST['observ']) ? $oActa->setObserv($_POST['observ']) : '';
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
} else { // editar.
	$oActa->setActa($acta);
	$oActa->DBCarregar();
	isset($_POST['id_asignatura']) ? $oActa->setId_asignatura($_POST['id_asignatura']) : '';
	isset($_POST['id_activ']) ? $oActa->setId_activ($_POST['id_activ']) : '';
	isset($_POST['libro']) ? $oActa->setLibro($_POST['libro']) : '';
	isset($_POST['pagina']) ? $oActa->setPagina($_POST['pagina']) : '';
	isset($_POST['linea']) ? $oActa->setLinea($_POST['linea']) : '';
	isset($_POST['lugar']) ? $oActa->setLugar($_POST['lugar']) : '';
	isset($_POST['observ']) ? $oActa->setObserv($_POST['observ']) : '';
	if ($oActa->DBGuardar() === false) {
		echo _('Hay un error, no se ha guardado');
	}
}
//para actualizar los examinadores.
//print_r($_POST['examinador']);
(empty($_POST['examinador']))? $n=0 : $n=count($_POST['examinador']);
for ($i=0; $i<$n; $i++) {
	//echo "examinadores: $n<br>";
	if (!empty($_POST['item'][$i])) { // miro si ya existía 
		$item=$_POST['item'][$i];
	    if (!empty($_POST['examinador'][$i])) { // si el nuevo no es nulo, lo actualizo
			$oActaTribunal->setId_item($item);
			$oActaTribunal->DBCarregar();
			$oActaTribunal->setExaminador($_POST['examinador'][$i]);
			$oActaTribunal->setOrden($i);
			if ($oActaTribunal->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
	    } else { // si es nulo lo elimino
			$oActaTribunal->setId_item($item);
			if ($oActaTribunal->DBEliminar() === false) {
				echo _('Hay un error, no se ha eliminado');
			}
	    } 
	} else { //No hay antiguo
	  if (!empty($_POST['examinador'][$i])) { // si hay nuevo: lo añado
			$oActaTribunal->setId_item('');
			$oActaTribunal->setActa($_POST['acta']);
			$oActaTribunal->setExaminador($_POST['examinador'][$i]);
			$oActaTribunal->setOrden($i);
			if ($oActaTribunal->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
	  }
	}
}	

$go_to = empty($_POST['go_to'])? "" : $_POST['go_to'];
if ($go_to=="acta_notas") {
	$go_to="acta_notas.php?id_activ=".$_POST['id_activ']."&id_asignatura=".$_POST['id_asignatura'];
}
//vuelve a la presentacion de la ficha.
//echo "gou: $go_to<br>";
$oPosicion = new web\Posicion();
$oPosicion->setId_div('ir_a');
echo $oPosicion->ir_a($go_to);
?>
