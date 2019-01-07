<?php
use actividades\model\entity\ActividadAll;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\ActividadProcesoTarea;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\ActividadTarea;
use procesos\model\entity\Proceso;


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
		$oActividad = new ActividadAll($Qid_activ);
		$oTipo = new TipoDeActividad(array('id_tipo_activ'=>$oActividad->getId_tipo_activ()));

		if ($oActividad->getDl_org() == ConfigGlobal::mi_dele()) {
			$id_tipo_proceso=$oTipo->getId_tipo_proceso();
		} else {
			$id_tipo_proceso=$oTipo->getId_tipo_proceso_ex();
		}

		$oActividadProceso=new GestorActividadProcesoTarea();
		$oActividadProceso->generar($Qid_activ,$id_tipo_proceso);
		break;
	case 'get':
		$GesActividadProceso=new GestorActividadProcesoTarea();
		$oLista = $GesActividadProceso->getActividadProcesoTareas(array('id_activ'=>$Qid_activ,'_ordre'=>'n_orden'));
		$txt='<table>';
		$txt.='<tr><td>'._('ok').'</td><td>'._('fase (tarea)').'</td><td>'._('responsable').'</td><td>'._('observaciones').'</td><td></td></tr>';
		foreach($oLista as $oActividadProcesoTarea) {
			$id_item = $oActividadProcesoTarea->getId_item();
			$id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
			$id_fase = $oActividadProcesoTarea->getId_fase();
			$id_tarea = $oActividadProcesoTarea->getId_tarea();
			$completado = $oActividadProcesoTarea->getCompletado();
			$observ = $oActividadProcesoTarea->getObserv();

			$oFase = new ActividadFase($id_fase);
			$fase = $oFase->getDesc_fase();
			$oTarea = new ActividadTarea($id_tarea);
			$tarea = $oTarea->getDesc_tarea();
			$chk= ($completado=='t')? 'checked': '';
			//buscar of responsable
			$oProceso = new Proceso(array('id_tipo_proceso'=>$id_tipo_proceso,
										'id_fase'=>$id_fase,
										'id_tarea'=>$id_tarea));
			$responsable=$oProceso->getOf_responsable();
			$txt.='<tr>';
			if (($_SESSION['oPerm']->have_perm($responsable))) {
				$txt.="<td><input type='checkbox' id='comp$id_item' name='completado' $chk></td>";
				$obs = "<td><input type='text' id='observ$id_item' name='observ' value='$observ' ></td>";
			} else {
				$icon = '';
				if ($completado == 't') {
					$icon = '<img src="'. ConfigGlobal::$web_icons .'/check.png" title="ok">';
				}
				$txt.="<td>$icon</td>";
				$obs = "<td></td>";
			}
			$txt.="<td>$fase ($tarea)</td>";
			$txt.="<td>$responsable</td>";
			$txt.= $obs;
			if (($_SESSION['oPerm']->have_perm($responsable))) {
				$txt.="<td><input type='button' name='b_guardar' value='"._('guardar')."' onclick='fnjs_guardar($id_item);'></td>";
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
			echo _('Hay un error, no se ha guardado');
		}
		break;
}
