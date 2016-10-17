<?php
use asignaturas\model as asignaturas;
use dossiers\model as dossiers;
use notas\model as notas;
use personas\model as personas;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_nom = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="p") { 
		$id_nivel=strtok($_POST['sel'][0],"#"); 
		$id_asignatura=strtok("#"); 
	}
}


switch($_POST['mod']) {
	case 'eliminar': //------------ BORRAR --------
		if ($_POST['pau']=="p") { 
			if (!empty($_POST['id_pau']) && !empty($id_asignatura) && !empty($id_nivel)) {
				$oPersonaNota = new notas\PersonaNota();
				$oPersonaNota->setId_nom($_POST['id_pau']);
				$oPersonaNota->setId_asignatura($id_asignatura);	
				$oPersonaNota->setId_nivel($id_nivel);	
				$oPersonaNota->DBCarregar(); //perque agafi els valors que ja té.
				if ($oPersonaNota->DBEliminar() === false) {
					echo _("Hay un error, no se ha borrado.");
				}
			}
		}
		//$go_to="dossiers_ver.php?pau=p&id_pau=".$_POST['id_pau']."&id_dossier=1011";
		break;
	case 'nuevo': //------------ NUEVO --------
		if ($_POST['id_asignatura']=='1' && $_POST['opcional'] == 'n') {
			$id_nivel = $_POST['id_nivel'];
			$oGesAsignaturas=new asignaturas\GestorAsignatura();
			$cAsignaturas=$oGesAsignaturas->getAsignaturas(array('id_nivel'=>$id_nivel));
			$oAsignatura = $cAsignaturas[0]; // sólo deberia haber una
			$id_asignatura=$oAsignatura->getId_asignatura();
		} else { //es una opcional
			empty($_POST['id_asignatura'])? $id_asignatura="" : $id_asignatura=$_POST['id_asignatura'];
			empty($_POST['id_nivel'])? $id_nivel="" : $id_nivel=$_POST['id_nivel'];
		}
		$oPersonaNota = new notas\PersonaNota();
		$oPersonaNota->setId_nivel($id_nivel);
		$oPersonaNota->setId_asignatura($id_asignatura);
		$oPersonaNota->setId_nom($_POST['id_pau']);
		// para saber a que schema pertenece la persona
		$oPersona = personas\Persona::NewPersona($id_nom);
		$id_schema = $oPersona->getId_schema();
		$oPersonaNota->setId_schema($id_schema);
		if (!empty($_POST['id_situacion'])) $oPersonaNota->setId_situacion($_POST['id_situacion']);
		if (!empty($_POST['f_acta'])) $oPersonaNota->setF_acta($_POST['f_acta']);
		// comprobar valor del acta
		if (!empty($_POST['acta'])) {
			$oActa = new notas\Acta();
			$valor = trim($_POST['acta']);
			$reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
			if (preg_match ($reg_exp, $valor) == 1) {
				} else {
					// inventar acta.
					$valor = $oActa->inventarActa($valor,$_POST['f_acta']);
				}
			$oPersonaNota->setActa($valor);
		}
		if (!empty($_POST['preceptor'])) $oPersonaNota->setPreceptor($_POST['preceptor']);
		if (!empty($_POST['id_preceptor'])) $oPersonaNota->setId_preceptor($_POST['id_preceptor']);
		if (!empty($_POST['detalle'])) $oPersonaNota->setDetalle($_POST['detalle']);
		if (!empty($_POST['epoca'])) $oPersonaNota->setEpoca($_POST['epoca']);
		if (!empty($_POST['id_activ'])) $oPersonaNota->setId_activ($_POST['id_activ']);
		if (!empty($_POST['nota_num'])) $oPersonaNota->setNota_num($_POST['nota_num']);
		if (!empty($_POST['nota_max'])) $oPersonaNota->setNota_max($_POST['nota_max']);
		if ($oPersonaNota->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado.");
		}
		// si no está abierto, hay que abrir el dossier para esta persona
		//abrir_dossier('p',$_POST['id_pau'],'1303',$oDB);
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$_POST['id_pau'],'id_tipo_dossier'=>1303));
		$oDossier->abrir();
		$oDossier->DBGuardar();

		break; 
	case 'editar':  //------------ EDITAR --------
		if (!empty($_POST['id_pau']) && !empty($_POST['id_asignatura_real'])) {
			$oPersonaNota = new notas\PersonaNota();
			$oPersonaNota->setId_nom($_POST['id_pau']);
			$oPersonaNota->setId_nivel($_POST['id_nivel']);	
			$oPersonaNota->DBCarregar(); //perque agafi els valors que ja té.
		} else {
			$oPersonaNota = new notas\PersonaNota();
		}
		$oPersonaNota->setId_situacion($_POST['id_situacion']);
		$oPersonaNota->setF_acta($_POST['f_acta']);
		// comprobar valor del acta
		if (!empty($_POST['acta'])) {
			$oActa = new notas\Acta();
			$valor = trim($_POST['acta']);
			$reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
			if (preg_match ($reg_exp, $valor) == 1) {
				} else {
					// inventar acta.
					$valor = $oActa->inventarActa($valor,$_POST['f_acta']);
				}
			$oPersonaNota->setActa($valor);
		}
		if (empty($_POST['preceptor'])) {
			$oPersonaNota->setPreceptor('');
			$oPersonaNota->setId_preceptor('');
		} else {
			$oPersonaNota->setPreceptor($_POST['preceptor']);
			$oPersonaNota->setId_preceptor($_POST['id_preceptor']);
		}
		$oPersonaNota->setDetalle($_POST['detalle']);
		$oPersonaNota->setEpoca($_POST['epoca']);
		$oPersonaNota->setId_activ($_POST['id_activ']);
		$oPersonaNota->setNota_num($_POST['nota_num']);
		$oPersonaNota->setNota_max($_POST['nota_max']);
		if ($oPersonaNota->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado.");
		}
		break;
}

if (!empty($_POST['go_to_que'])) {
	switch ($_POST['go_to_que']) {
		case 1: // no mas notas
			$oPosicion->setId_div('ir_a');
			echo $oPosicion->atras();
			break;
		case 2: // otra nota
			empty($_POST['go_to_1'])? $go_to="" : $go_to=$_POST['go_to_1'];
			echo $oPosicion->ir_a($go_to);
			break;
	}
} else {
	$oPosicion->setId_div('ir_a');
	echo $oPosicion->atras();
}
?>
