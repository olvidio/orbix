<?php
use core\ConfigGlobal;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\ActividadProcesoTarea;
use procesos\model\entity\ActividadTarea;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorTareaProceso;


// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)  \filter_input(INPUT_POST, 'que');
$Qid_activ = (integer)  \filter_input(INPUT_POST, 'id_activ');

switch($Qque) {
	case 'generar':
		$oActividadProceso=new GestorActividadProcesoTarea();
		$oActividadProceso->generarProceso($Qid_activ);
		break;
	case 'get':
		$GesActividadProceso=new GestorActividadProcesoTarea();
		$oLista = $GesActividadProceso->getActividadProcesoTareas(array('id_activ'=>$Qid_activ,'_ordre'=>'n_orden'));
		$txt='<table>';
		$txt.='<tr><th>'._("ok").'</th><th>'._("fase (tarea)").'</th><th>'._("responsable").'</th><th>'._("observaciones").'</th><th></th></tr>';
		foreach($oLista as $oActividadProcesoTarea) {
			$id_item = $oActividadProcesoTarea->getId_item();
			$id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
			$id_fase = $oActividadProcesoTarea->getId_fase();
			$id_tarea = $oActividadProcesoTarea->getId_tarea();
			$completado = $oActividadProcesoTarea->getCompletado();
			$observ = $oActividadProcesoTarea->getObserv();

			$oFase = new ActividadFase($id_fase);
			$fase = $oFase->getDesc_fase();
			if (empty($fase)) { continue; } // No existe
			$oTarea = new ActividadTarea($id_tarea);
			$tarea = $oTarea->getDesc_tarea();
			$chk= ($completado=='t')? 'checked': '';
			//buscar of responsable
			$GesTareaProceso = new GestorTareaProceso();
			$cTareasProceso = $GesTareaProceso->getTareasProceso(['id_tipo_proceso'=>$id_tipo_proceso,
			                                                'id_fase'=>$id_fase,
			                                                'id_tarea'=>$id_tarea]);
			// sólo debería haber uno
			if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
			} else {
			    $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"),$id_tipo_proceso,$id_fase,$id_tarea);
                exit($msg_err);
			}
			$responsable = $oTareaProceso->getOf_responsable();
			$txt.='<tr>';
			if (($_SESSION['oPerm']->have_perm($responsable))) {
				$txt.="<td><input type='checkbox' id='comp$id_item' name='completado' $chk></td>";
				$obs = "<td><input type='text' id='observ$id_item' name='observ' value='$observ' ></td>";
			} else {
				$icon = '';
				if ($completado == 't') {
					$icon = '<img src="'. ConfigGlobal::getWeb_icons() .'/checkbox-checked.png" title="ok">';
				} else {
					$icon = '<img src="'. ConfigGlobal::getWeb_icons() .'/check-box-outline-blank.png" title="">';
				}
				$txt.="<td>$icon</td>";
				$obs = "<td></td>";
			}
			$txt_fase = empty($tarea)? '' : "($tarea)";
			$txt.="<td>$fase $txt_fase</td>";
			$txt.="<td>$responsable</td>";
			$txt.= $obs;
			if (($_SESSION['oPerm']->have_perm($responsable))) {
				$txt.="<td><input type='button' name='b_guardar' value='"._("guardar")."' onclick='fnjs_guardar($id_item);'></td>";
			}
			$txt .= '</tr>';
		}
		$txt.='</table>';
		echo $txt;
		break;
	case 'update':
        $Qid_item = (integer)  \filter_input(INPUT_POST, 'id_item');
        $Qcompletado = (string)  \filter_input(INPUT_POST, 'completado');
        $Qobserv = (string)  \filter_input(INPUT_POST, 'observ');
        
		$oFicha = new ActividadProcesoTarea(array('id_item'=>$Qid_item));
		$oFicha->DBCarregar(); // perque tingui tots els valors, y no esbori al grabar.
		$oFicha->setCompletado($Qcompletado);	
		$oFicha->setObserv($Qobserv);	
		if ($oFicha->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
		}
		break;
}
