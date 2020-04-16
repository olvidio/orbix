<?php
use actividades\model\entity\ActividadAll;
use menus\model\PermisoMenu;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\ActividadTarea;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorActividadTarea;
use procesos\model\entity\GestorTareaProceso;
use procesos\model\entity\TareaProceso;
use usuarios\model\entity\Usuario;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');

switch($Qque) {
    case 'regenerar':
        // para cada fase del proceso
        // mirar que actividades les falta y añadir.
	    $Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');
		$GesTareaPorceso = new GestorTareaProceso();
		$cTareasProceso = $GesTareaPorceso->getTareasProceso(['id_tipo_proceso'=>$Qid_tipo_proceso]);
		$i=0;
        $gesActividadProcesoTarea =  new GestorActividadProcesoTarea();
		foreach ($cTareasProceso as $oTareaProceso) {
			$i++;
			$id_item=$oTareaProceso->getId_item();
			$id_fase = $oTareaProceso->getId_fase();
			$id_tarea = $oTareaProceso->getId_tarea();
			$gesActividadProcesoTarea->añadirFaseTarea($Qid_tipo_proceso,$id_fase,$id_tarea);
		}
        // buscar las fases a eliminar
        $gesActividadProcesoTarea->borrarFaseTareaInexistente($Qid_tipo_proceso,$id_fase,$id_tarea);
        
        break;
    case 'clonar':
	    $Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');
	    $Qid_tipo_proceso_ref = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso_ref');
        
	    // borrar lo anterior:
		$GesTareaPorceso = new GestorTareaProceso();
		$cTareasProceso = $GesTareaPorceso->getTareasProceso(array('id_tipo_proceso'=>$Qid_tipo_proceso));
		foreach ($cTareasProceso as $oTareaProceso) {
            $oTareaProceso->DBEliminar();
		}
	    // clonar 
		$GesTareaPorceso = new GestorTareaProceso();
		$cTareasProceso = $GesTareaPorceso->getTareasProceso(array('id_tipo_proceso'=>$Qid_tipo_proceso_ref));
		$i=0;
		foreach ($cTareasProceso as $oTareaProceso) {
            $id_fase = $oTareaProceso->getId_fase();
            $id_tarea = $oTareaProceso->getId_tarea();
            $status = $oTareaProceso->getStatus();
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $json_fases_previas = $oTareaProceso->getJson_fases_previas();
            
            $oTareaProcesoNew = new TareaProceso();
            $oTareaProcesoNew->setId_tipo_proceso($Qid_tipo_proceso);
            $oTareaProcesoNew->setId_fase($id_fase);
            $oTareaProcesoNew->setId_tarea($id_tarea);
            $oTareaProcesoNew->setStatus($status);
            $oTareaProcesoNew->setId_of_responsable($id_of_responsable);
            $oTareaProcesoNew->setJson_fases_previas($json_fases_previas);
            $oTareaProcesoNew->DBGuardar();
		}
		// Omito el break, para que a haga el get.
	case 'get':
	    $Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');
	    $oActividad = new ActividadAll();
		$a_status= $oActividad->getArrayStatus();
			
		$oMiUsuario = new Usuario(core\ConfigGlobal::mi_id_usuario());
		$miSfsv = core\ConfigGlobal::mi_sfsv();
		
		// para crear un desplegable de oficinas. Uso los de los menus
		$oPermMenus = new PermisoMenu;
		$aOpcionesOficinas = $oPermMenus->lista_array();
		
		if ($oMiUsuario->isRole('SuperAdmin')) { // Es administrador
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

		$GesTareaPorceso = new GestorTareaProceso();
		$cTareasProceso = $GesTareaPorceso->getTareasProceso(['id_tipo_proceso'=>$Qid_tipo_proceso,'_ordre'=>'status,id_of_responsable']);
		$txt = '<table>';
        $txt .= '<tr><th>'._("status").'</th><th>'._("responsable").'</th>';
        $txt .= '<th colspan=3>'._("fase - tarea").'</th><th>'._("modificar").'</th><th>'._("eliminar").'</th></tr>';
		$i=0;
		foreach ($cTareasProceso as $oTareaProceso) {
			$i++;
			$clase = ($i%2 == 0)? 'tono2' : 'tono4'; 
			$id_item=$oTareaProceso->getId_item();
			$status=$oTareaProceso->getStatus();
			$status_txt=$a_status[$status];
			$id_of_responsable=$oTareaProceso->getId_of_responsable();
			$responsable = empty($aOpcionesOficinas[$id_of_responsable])? '' : $aOpcionesOficinas[$id_of_responsable];
			
			$oFase = new ActividadFase($oTareaProceso->getId_fase());
			$fase=$oFase->getDesc_fase();
			$sf=($oFase->getSf())? 2 : 0;
			$sv=($oFase->getSv())? 1 : 0;
			//ojo, que puede ser las dos a la vez
			if (!(($soy & $sf) OR ($soy & $sv))) {
			    $i--;
			    continue; 
			}
			$oTarea = new ActividadTarea($oTareaProceso->getId_tarea());
			$tarea = $oTarea->getDesc_tarea();
			$tarea_txt = empty($tarea)? '' : "($tarea)";
			$fase_previa = '';
            $tarea_previa_txt = '';
			$aFases_previas = $oTareaProceso->getJson_fases_previas(TRUE);
			foreach ($aFases_previas as $oFaseP) {
			    $id_fase_previa = $oFaseP['id_fase'];
			    if (empty($id_fase_previa)) continue;
			    //$id_tarea_previa = $oFaseP['id_tarea'];
			    //$mensaje_requisito = $oFaseP['mensaje'];
                $oFase_previa = new ActividadFase($id_fase_previa);
                $fase_previa .= empty($fase_previa)? '' : ' '._("y").' ';
                $fase_previa .= $oFase_previa->getDesc_fase();
                $tarea_previa_txt = empty($tarea_previa)? '' : "($tarea_previa)";
			}
			
			$mod="<span class=link onclick=fnjs_modificar($id_item) title='"._("modificar")."' >"._("modificar")."</span>";
			$drop="<span class=link onclick=fnjs_eliminar($id_item) title='"._("eliminar")."' >"._("eliminar")."</span>";

			$txt.="<tr class=$clase><td>($status_txt)</td><td>$responsable</td><td colspan=3>$fase $tarea_txt</td><td>$mod</td><td>$drop</td></tr>";
			$txt.="<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;"._("requisito").":</td><td>$fase_previa $tarea_previa_txt</td></tr>";
		}
		$txt.='</table>';
		echo $txt;
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
	    $Qstatus = (integer) \filter_input(INPUT_POST, 'status');
	    $Qid_of_responsable = (integer) \filter_input(INPUT_POST, 'id_of_responsable');
	    $Qid_fase = (integer) \filter_input(INPUT_POST, 'id_fase');
	    $Qid_tarea = (integer) \filter_input(INPUT_POST, 'id_tarea');
        // arrays
        $Qid_fase_previa = (array)  \filter_input(INPUT_POST, 'id_fase_previa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qid_tarea_previa = (array)  \filter_input(INPUT_POST, 'id_tarea_previa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qmensaje_requisito = (array)  \filter_input(INPUT_POST, 'mensaje_requisito', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $aFases_previas = [];
        $num_fases_previas = count($Qid_fase_previa);
        for ($i=0; $i<$num_fases_previas; $i++) {
            $oFase_previa = [];
            $oFase_previa['id_fase'] = $Qid_fase_previa[$i];
            $oFase_previa['id_tarea'] = $Qid_tarea_previa[$i];
            $oFase_previa['mensaje'] = $Qmensaje_requisito[$i];
            if (empty($Qid_fase_previa[$i])) continue;
            $aFases_previas[] = $oFase_previa;
        }
		if (empty($Qid_tarea)) $Qid_tarea=0; // no puede ser NULL.

		$oTareaProceso = new TareaProceso(array('id_item'=>$Qid_item));
		$oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);	
		$oTareaProceso->setStatus($Qstatus);	
		$oTareaProceso->setId_of_responsable($Qid_of_responsable);	
		$oTareaProceso->setId_fase($Qid_fase);	
		$oTareaProceso->setId_tarea($Qid_tarea);	
		$oTareaProceso->setJson_fases_previas($aFases_previas);	
		if ($oTareaProceso->DBGuardar() === false) {
			echo _("hay un error, no se ha guardado");
			echo "\n".$oTareaProceso->getErrorTxt();
		}
		break;
	case 'eliminar':
	    $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
		$oTareaProceso = new TareaProceso(array('id_item'=>$Qid_item));
		if ($oTareaProceso->DBEliminar() === false) {
			echo _("hay un error, no se ha eliminado");
			echo "\n".$oTareaProceso->getErrorTxt();
		}
		break;
}