<?php
use usuarios\model\entity as usuarios;
use menus\model\entity as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$que=empty($_POST['que'])? '' : $_POST['que'];
switch($que) {
	case 'del_grupmenu':
		if (isset($_POST['sel'])) { //vengo de un checkbox
			foreach ($_POST['sel'] as $sel) {
				//$id_nom=$sel[0];
				$id_item=strtok($sel,"#");
				$oGrupMenuRole = new menus\GrupMenuRole($id_item);
				if ($oGrupMenuRole->DBEliminar() === false) {
					echo _('Hay un error, no se ha eliminado');
				}
			} 
		}
		break;
	case 'add_grupmenu':
		if (isset($_POST['sel'])) { //vengo de un checkbox
			foreach ($_POST['sel'] as $sel) {
				//$id_nom=$sel[0];
				$id_role=strtok($sel,"#");
				$id_grupmenu=strtok("#");
				$oGrupMenuRole = new menus\GrupMenuRole();
				$oGrupMenuRole->setId_role($id_role);
				$oGrupMenuRole->setId_grupmenu($id_grupmenu);
				if ($oGrupMenuRole->DBGuardar() === false) {
					echo _('Hay un error, no se ha guardado');
				}
			} 
		}
		break;
	case "guardar":
		if ($_POST['role']) {
			$oRole = new usuarios\Role(array('id_role' => $_POST['id_role']));
			$oRole->setRole($_POST['role']);
			$sf = !empty($_POST['sf'])? '1' : 0;
			$oRole->setSf($sf);
			$sv = !empty($_POST['sv'])? '1' : 0;
			$oRole->setSv($sv);
			$oRole->setPau($_POST['pau']);
			if ($oRole->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		} else { exit("debe poner un nombre"); }
	break;
	case "nuevo":
		if ($_POST['role']) {
			$oRole = new usuarios\Role();
			$oRole->setRole($_POST['role']);
			$sf = !empty($_POST['sf'])? '1' : 0;
			$oRole->setSf($sf);
			$sv = !empty($_POST['sv'])? '1' : 0;
			$oRole->setSv($sv);
			$oRole->setPau($_POST['pau']);
			if ($oRole->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
		} else { exit("debe poner un nombre"); }
		break;
}
