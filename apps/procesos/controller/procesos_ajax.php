<?php
use actividades\model\entity\ActividadAll;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\ActividadTarea;
use procesos\model\entity\GestorActividadTarea;
use procesos\model\entity\GestorProceso;
use procesos\model\entity\Proceso;
use usuarios\model\entity\Usuario;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');

switch($Qque) {
	case 'get':
	    $Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');
		$a_status= ActividadAll::ARRAY_STATUS_TXT;
			
		$oMiUsuario = new Usuario(core\ConfigGlobal::mi_id_usuario());
		$miRole = $oMiUsuario->getId_role();
		$miSfsv = core\ConfigGlobal::mi_sfsv();

		if ($miRole == 1) { // Es administrador
		   	$soy = 3;
		} else {
			// filtro por sf/sv
			switch ($miSfsv) {
				case 1: // sv
					$soy = 1;
					break;
				case 2: //sf
					$soy = 2;
					break;
			}
		}

		$oLista = new GestorProceso();
		$oColection = $oLista->getProcesos(array('id_tipo_proceso'=>$Qid_tipo_proceso,'_ordre'=>'n_orden'));
		$txt='<table>';
		$i=0;
		foreach ($oColection as $oProceso) {
			$i++;
			$id_item=$oProceso->getId_item();
			$status=$oProceso->getStatus();
			$status_txt=$a_status[$status];
			$responsable=$oProceso->getOf_responsable();
			$oFase = new ActividadFase($oProceso->getId_fase());
			$fase=$oFase->getDesc_fase();
			$sf=($oFase->getSf())? 2 : 0;
			$sv=($oFase->getSv())? 1 : 0;
			//ojo, que puede ser las dos a la vez
			if (!(($soy & $sf) OR ($soy & $sv))) continue; 
			$oTarea = new ActividadTarea($oProceso->getId_tarea());
			$tarea=$oTarea->getDesc_tarea();
			$oFase_previa = new ActividadFase($oProceso->getId_fase_previa());
			$fase_previa=$oFase_previa->getDesc_fase();
			$oTarea_previa = new ActividadTarea($oProceso->getId_tarea_previa());
			$tarea_previa=$oTarea_previa->getDesc_tarea();
			$mod="<span class=link onclick=fnjs_modificar($id_item) title='"._('modificar')."' >"._('modificar')."</span>";
			$drop="<span class=link onclick=fnjs_eliminar($id_item) title='"._('eliminar')."' >"._('eliminar')."</span>";
			$up="<span class=link onclick=fnjs_mover($id_item,'up') title='"._('mover hacia arriba')."' >+</span>";
			$down="<span class=link onclick=fnjs_mover($id_item,'down') title='"._('mover hacia abajo')."' >-</span>";

			$txt.="<tr><td>($status_txt)</td><td>$i</td><td>$responsable</td><td colspan=3>$fase ($tarea)</td><td>$mod</td><td>$up</td><td>$down</td><td>$drop</td></tr>";
			$txt.="<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;"._('requisito').":</td><td>$fase_previa ($tarea_previa)</td></tr>";
		}
		$txt.='</table>';
		echo $txt;
		break;
	case 'orden':
	    $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
	    $Qorden = (string) \filter_input(INPUT_POST, 'orden');
		$oLista = new GestorProceso();
		$rta = $oLista->setProcesosOrden($Qid_item,$Qorden);
		$error = '';
		if ($rta === false) {
		    $error = _('Hay un error, no se ha movido');
		}
		echo trim($error);
		break;
	case 'depende':
	    $Qacc = (string) \filter_input(INPUT_POST, 'acc');
	    $Qvalor_depende = (string) \filter_input(INPUT_POST, 'valor_depende');
		//caso de actualizar el campo depende
		if ($Qacc == '#id_tarea') {
			$oDepende = new GestorActividadTarea();
			$oDesplegable = $oDepende->getListaActividadTareas($Qvalor_depende);
			if (is_object($oDesplegable)) {
				$oDesplegable->setBlanco(true);
				echo $oDesplegable->options();
			} else { echo ""; }
		}
		if ($Qacc == '#id_tarea_previa') {
			$oDepende = new GestorActividadTarea();
			$oDesplegable = $oDepende->getListaActividadTareas($Qvalor_depende);
			$oDesplegable->setBlanco(true);
			echo $oDesplegable->options();
		}
		break;
	case 'update':
	    $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
	    $Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');
	    $Qn_orden = (integer) \filter_input(INPUT_POST, 'n_orden');
	    $Qstatus = (integer) \filter_input(INPUT_POST, 'status');
	    $Qof_responsable = (string) \filter_input(INPUT_POST, 'of_responsable');
	    $Qmensaje_requisito = (string) \filter_input(INPUT_POST, 'mensaje_requisito');
	    $Qid_fase = (integer) \filter_input(INPUT_POST, 'id_fase');
	    $Qid_tarea = (integer) \filter_input(INPUT_POST, 'id_tarea');
	    $Qid_fase_previa = (integer) \filter_input(INPUT_POST, 'id_fase_previa');
	    $Qid_tarea_previa = (integer) \filter_input(INPUT_POST, 'id_tarea_previa');

		if (empty($Qid_tarea)) $Qid_tarea=0; // no puede ser NULL.

		$oFicha = new Proceso(array('id_item'=>$Qid_item));
		$oFicha->setId_tipo_proceso($Qid_tipo_proceso);	
		$oFicha->setN_orden($Qn_orden);	
		$oFicha->setStatus($Qstatus);	
		$oFicha->setOf_responsable($Qof_responsable);	
		$oFicha->setMensaje_requisito($Qmensaje_requisito);	
		$oFicha->setId_fase($Qid_fase);	
		$oFicha->setId_tarea($Qid_tarea);	
		$oFicha->setId_fase_previa($Qid_fase_previa);	
		$oFicha->setId_tarea_previa($Qid_tarea_previa);	
		if ($oFicha->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	case 'eliminar':
	    $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
		$oFicha = new Proceso(array('id_item'=>$Qid_item));
		if ($oFicha->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		break;
}