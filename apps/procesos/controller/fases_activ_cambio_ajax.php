<?php
use actividades\model\entity\GestorActividad;
use actividades\model\entity\TipoDeActividad;
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorProceso;
use procesos\model\entity\GestorActividadProcesoTarea;
use web\Lista;
use web\Periodo;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string) \filter_input(INPUT_POST, 'dl_propia');
$Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');

switch($Qque) {
	case 'lista':
		$id_fase_nueva = empty($_POST['id_fase_nueva'])? '' : $_POST['id_fase_nueva'];
		if (empty($id_fase_nueva)) exit('<h2>'._('Debe poner la fase nueva').'</h2>');

		$Qque = empty($_POST['que'])? '' : $_POST['que'];
		$Qstatus = empty($_POST['status'])? 2 : $_POST['status'];
		$Qid_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];
		$Qperiodo = empty($_POST['periodo'])? '' : $_POST['periodo'];
		$Qinicio = empty($_POST['inicio'])? '' : $_POST['inicio'];
		$Qfin = empty($_POST['fin'])? '' : $_POST['fin'];
		$Qyear = empty($_POST['year'])? '' : $_POST['year'];
		$Qempiezamin = empty($_POST['empiezamin'])? date('d/m/Y',mktime(0, 0, 0, date('m'), date('d')-40, date('Y'))) : $_POST['empiezamin'];
		$Qempiezamax = empty($_POST['empiezamax'])? date('d/m/Y',mktime(0, 0, 0, date('m')+6, 0, date('Y'))) : $_POST['empiezamax'];
		// id_tipo_activ
		if ($Qid_tipo_activ!='......') {
			$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
			$aOperador['id_tipo_activ'] = '~';
		}
		// dl_org
		if ($_POST['dl_propia'] == 't') {
			$aWhere['dl_org'] = ConfigGlobal::$dele;
		} else {
			$aWhere['dl_org'] = ConfigGlobal::$dele;
			$aOperador['dl_org'] = '!=';
		}
		// las borrables no
		$aWhere['status'] = 4;
		$aOperador['status'] = '<';


		// periodo.
		if (empty($Qperiodo) || $Qperiodo == 'otro') {
			$Qinicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
			$Qfin = empty($Qfin)? $Qempiezamax : $Qfin;
		} else {
			$oPeriodo = new Periodo();
			$any=empty($Qyear)? date('Y')+1 : $Qyear;
			$oPeriodo->setAny($any);
			$oPeriodo->setPeriodo($Qperiodo);
			$Qinicio = $oPeriodo->getF_ini();
			$Qfin = $oPeriodo->getF_fin();
		}
		if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
			$aWhere['f_fin'] = "'$Qinicio','$Qfin'";
			$aOperador['f_fin'] = 'BETWEEN';
		} else {
			$aWhere['f_ini'] = "'$Qinicio','$Qfin'";
			$aOperador['f_ini'] = 'BETWEEN';
		}
		$oActividades=new GestorActividad();

		$i=0;
		$pagina=0;
		$a_cabeceras=array();
		$a_cabeceras[] = _('nom');
		$a_cabeceras[] = _('última fase completada');
		$a_cabeceras[] = _('cumple requisito');
		
		$a_botones=array( 
			array( 'txt' => _('cambiar los marcados'), 'click' =>"fnjs_cambiar(\"#seleccionados\")" ) );

		$a_valores=array();
		$aWhere['_ordre'] = 'f_ini';
		$cActividades = $oActividades->getActividades($aWhere,$aOperador);
		$num_activ=count($cActividades);
		$num_ok = 0;
		foreach($oActividades->getActividades($aWhere,$aOperador) as $oActividad) {
			//print_r($oActividad);
			extract($oActividad->getTot());
			$i++;
			// Por el tipo de actividad sé el tipo de proceso
			$oTipoActiv = new TipoDeActividad(array('id_tipo_activ'=>$id_tipo_activ));
			$id_tipo_proceso = $oTipoActiv->getId_tipo_proceso();
			// miro cual es la tarea previa.
			$GesProceso = new GestorProceso();
			$cProcesos = $GesProceso->getProcesos(array('id_tipo_proceso'=>$id_tipo_proceso,'id_fase'=>$id_fase_nueva));
			foreach ($cProcesos as $oProceso) {
				$id_fase_previa = $oProceso->getId_fase_previa();
				// Busco el proceso de esta actividad
				$GesActivProceso = new GestorActividadProcesoTarea();
				$id_fase_actual = $GesActivProceso->faseActualAcabada($id_activ); // también posible 'START' y 'SIN'
				// miro si tiene la fase requerida.
				if (!empty($id_fase_previa)) {
					$cActivProceso = $GesActivProceso->getActividadProcesoTareas(array('id_activ'=>$id_activ,'id_fase'=>$id_fase_previa));
					$fase_previa_completado = $cActivProceso[0]->getCompletado(); // sólo uno
					if ($fase_previa_completado == 't') {
						$mensaje_requisito = 'ok';
						$num_ok++;
						if ($id_fase_nueva == $id_fase_actual) { $mensaje_requisito = '='; $num_ok--; }
					} else {
						$mensaje_requisito = '<span style="color:red;">'.$oProceso->getMensaje_requisito().'</span>';
					}
				} else {
					$mensaje_requisito = 'ok'; //si no tiene fase previa, ok requisito.
					$num_ok++;
				}
				if ($id_fase_actual == 'START') {
					$fase_actual = _('por empezar');
				} else {
					$oActividadFase = new ActividadFase($id_fase_actual);
					$fase_actual = $oActividadFase->getDesc_fase();
				}

				// mostrar lista
				$chk = ($mensaje_requisito == 'ok')? 'checked' : '';
				$a_valores[$i]['sel'] = array( 'select'=>$chk, 'id'=>$id_activ);
				$a_valores[$i][1]= $nom_activ;
				$a_valores[$i][2]= $fase_actual;
				$a_valores[$i][3]= $mensaje_requisito;
			}

		}

		$oTabla = new Lista();
		$oTabla->setId_tabla('actividades_fases_cambio_ajax');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);

		$msg = sprintf(_('%s actividades, %s para cambiar'),$num_activ,$num_ok);
		echo $msg;
		$txt = '<form id="seleccionados" name="seleccionados" action="" method="post">';
		$txt .= '<input type="hidden" id="frm_id_fase_nueva" name="id_fase_nueva" value="'.$id_fase_nueva.'">';
		$txt .= '<input type="hidden" id="que" name="que" value="update">';
		$txt .= $oTabla->mostrar_tabla();
		$txt .= '</form>';

		echo $txt;
		break;
	case 'update':
		$id_fase_nueva = $_POST['id_fase_nueva'];
		foreach ($_POST['sel'] as $id_activ) {
			$id_activ=strtok($id_activ,"#");
			// selecciono todas las tareas de esta fase.
			$GesActividadProceso=new GestorActividadProcesoTarea();
			$oLista = $GesActividadProceso->getActividadProcesoTareas(array('id_activ'=>$id_activ,'id_fase'=>$id_fase_nueva,'_ordre'=>'n_orden'));
			foreach($oLista as $oActividadProcesoTarea) {
				$oActividadProcesoTarea->DBCarregar(); // perque tingui tots els valors, y no esbori al grabar.
				$oActividadProcesoTarea->setCompletado('t');
				if ($oActividadProcesoTarea->DBGuardar() === false) {
					echo _('Hay un error, no se ha guardado');
				}
			}
		}
		break;
	case 'get':
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ,$Qdl_propia);

		$oGesFases= new GestorActividadFase();
		$oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos,true);
		$oDesplFasesIni->setNombre('id_fase_nueva');
		$txt = '';
		if (isset($oDesplFasesIni)) {
			$txt .= $oDesplFasesIni->desplegable();
		}
		echo $txt;
		break;
}
