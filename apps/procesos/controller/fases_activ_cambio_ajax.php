<?php
use actividades\model\entity\GestorActividadDl;
use actividades\model\entity\GestorActividadPub;
use actividades\model\entity\GestorTipoDeActividad;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorTareaProceso;
use web\Lista;
use web\Periodo;
use actividades\model\entity\Actividad;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string) \filter_input(INPUT_POST, 'dl_propia');
//$Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');

switch($Qque) {
	case 'lista':
        $Qid_fase_nueva = (string) \filter_input(INPUT_POST, 'id_fase_nueva');
		if (empty($Qid_fase_nueva)) exit('<h2>'._("Debe poner la fase nueva").'</h2>');

		$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
		$Qyear = (string) \filter_input(INPUT_POST, 'year');
		$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
		$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

		// valores por defeccto
		if (empty($Qperiodo)) {
		    $Qperiodo = 'actual';
		}
		
		$refresh = $oPosicion->getParametro('refresh',1);
		if (empty($refresh)) {
            $oPosicion->recordar();
            $refresh = 1;
		}
		$aGoBack = array (
		    'refresh' => $refresh,
		    'hnov' => 0, // Como vengo de un liknkSinVal, está a 1, y no se borra.
		    'que' => $Qque,
		    'dl_propia'=>$Qdl_propia,
		    'id_fase_nueva'=>$Qid_fase_nueva,
		    'id_tipo_activ'=>$Qid_tipo_activ,
		    'periodo'=>$Qperiodo,
		    'year'=>$Qyear,
		    'empiezamin'=>$Qempiezamin,
		    'empiezamax'=>$Qempiezamax );
		$oPosicion->setParametros($aGoBack,1);
		
		$aWhere = [];
		$aOperador = [];
		// id_tipo_activ
		if ($Qid_tipo_activ!='......') {
			$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
			$aOperador['id_tipo_activ'] = '~';
		}
		// dl_org
		if ($Qdl_propia == 't') {
			$aWhere['dl_org'] = ConfigGlobal::mi_delef();
    		$oActividades = new GestorActividadDl();
		} else {
			$aWhere['dl_org'] = ConfigGlobal::mi_delef();
			$aOperador['dl_org'] = '!=';
    		$oActividades = new GestorActividadPub();
		}
		// las borrables no
		$aWhere['status'] = 4;
		$aOperador['status'] = '<';

		// periodo.
		$oPeriodo = new Periodo();
		$oPeriodo->setDefaultAny('next');
		$oPeriodo->setAny($Qyear);
		$oPeriodo->setEmpiezaMin($Qempiezamin);
		$oPeriodo->setEmpiezaMax($Qempiezamax);
		$oPeriodo->setPeriodo($Qperiodo);
		
		$inicioIso = $oPeriodo->getF_ini_iso();
		$finIso = $oPeriodo->getF_fin_iso();
		if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
			$aWhere['f_fin'] = "'$inicioIso','$finIso'";
			$aOperador['f_fin'] = 'BETWEEN';
		} else {
			$aWhere['f_ini'] = "'$inicioIso','$finIso'";
			$aOperador['f_ini'] = 'BETWEEN';
		}

		$i=0;
		$a_cabeceras=array();
		$a_cabeceras[] = _("nom");
		$a_cabeceras[] = _("última fase completada");
		$a_cabeceras[] = _("cumple requisito");
		
		$a_botones=[
                ['txt' => _("cambiar los marcados"), 'click' =>"fnjs_cambiar(\"#seleccionados\")" ],
                ['txt' => _("ver proceso actividad"), 'click' =>"fnjs_ver_activ(\"#seleccionados\")" ],
		    ];

		$a_valores=array();
		$aWhere['_ordre'] = 'f_ini';
		$cActividades = $oActividades->getActividades($aWhere,$aOperador);
		if (!is_array($cActividades)) {
		    exit (_("faltan condiciones para la selección"));
		}
		$num_activ=count($cActividades);
		$num_ok = 0;
		foreach($oActividades->getActividades($aWhere,$aOperador) as $oActividad) {
			//print_r($oActividad);
			$id_activ = $oActividad->getId_activ();
			$id_tipo_activ = $oActividad->getId_tipo_activ();
			$nom_activ = $oActividad->getNom_activ();
			$i++;
			// Por el tipo de actividad sé el tipo de proceso
			$oTipoActiv = new TipoDeActividad(array('id_tipo_activ'=>$id_tipo_activ));
			$id_tipo_proceso = $oTipoActiv->getId_tipo_proceso();
			// miro cual es la tarea previa.
			$GesTareaProceso = new GestorTareaProceso();
			$cTareasProceso = $GesTareaProceso->getTareasProceso(array('id_tipo_proceso'=>$id_tipo_proceso,'id_fase'=>$Qid_fase_nueva));
			foreach ($cTareasProceso as $oTareaProceso) {
				$id_fase_previa = $oTareaProceso->getId_fase_previa();
				// Busco el proceso de esta actividad
				$GesActivProceso = new GestorActividadProcesoTarea();
				$id_fase_actual = $GesActivProceso->faseActualAcabada($id_activ); // también posible 'START' y 'SIN'
				// miro si tiene la fase requerida.
				if (!empty($id_fase_previa)) {
					$cActivProceso = $GesActivProceso->getActividadProcesoTareas(array('id_activ'=>$id_activ,'id_fase'=>$id_fase_previa));
					if (empty($cActivProceso)) {
						$mensaje_requisito = $oTareaProceso->getMensaje_requisito();
						$a_valores[$i]['clase']='wrong';
					} else {
                        $fase_previa_completado = $cActivProceso[0]->getCompletado(); // sólo uno
                        if ($fase_previa_completado == 't') {
                            $mensaje_requisito = 'ok';
                            $num_ok++;
                            if ($Qid_fase_nueva == $id_fase_actual) { $mensaje_requisito = '='; $num_ok--; }
                        } else {
                            $mensaje_requisito = $oTareaProceso->getMensaje_requisito();
                            $a_valores[$i]['clase']='wrong';
                        }
					}
				} else {
					$mensaje_requisito = 'ok'; //si no tiene fase previa, ok requisito.
					$num_ok++;
				}
				if ($id_fase_actual == 'START') {
					$fase_actual = _("por empezar");
				} else {
					$oActividadFase = new ActividadFase($id_fase_actual);
					$fase_actual = $oActividadFase->getDesc_fase();
				}
				if (empty($fase_actual)) {
				    $fase_actual = _("no existe. Debe crear el proceso");
				}

				// mostrar lista
				if ($mensaje_requisito == 'ok') {
                    $a_valores['select'][] = $id_activ;
				}
				$a_valores[$i]['sel'] = $id_activ;
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
		
		$oHash = new web\Hash();
		$oHash->setcamposForm('sel');
		$oHash->setcamposNo('scroll_id');
		$a_camposHidden = array(
		    'id_fase_nueva' => $Qid_fase_nueva,
		    'que' => 'update',
		);
		$oHash->setArraycamposHidden($a_camposHidden);
		
		$msg = sprintf(_("%s actividades, %s para cambiar"),$num_activ,$num_ok);
		
		$txt = '<form id="seleccionados" name="seleccionados" action="" method="post">';
		$txt .= $oHash->getCamposHtml();
		$txt .= $oTabla->mostrar_tabla();
		$txt .= '</form>';

		echo $msg;
		echo $txt;
		break;
	case 'update':
        $Qid_fase_nueva = (string) \filter_input(INPUT_POST, 'id_fase_nueva');
        $a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
		foreach ($a_sel as $id_activ) {
			$id_activ=strtok($id_activ,"#");
			$GesActividadProceso = new GestorActividadProcesoTarea();
			// selecciono todas las tareas de esta fase.
			$cLista = $GesActividadProceso->getActividadProcesoTareas(array('id_activ'=>$id_activ,'_ordre'=>'n_orden'));
			$cListaSel = $GesActividadProceso->getActividadProcesoTareas(array('id_activ'=>$id_activ,'id_fase'=>$Qid_fase_nueva, '_ordre'=>'n_orden'));
			if (empty($cListaSel)) {
			    // No se encuentra esta fase para esta actividad
			    $oActividad = new Actividad($id_activ);
			    $nom_activ = $oActividad->getNom_activ();
			    $txt = sprintf(_("No se encuentra esta fase %s para esta actividad %s(%s)"),$Qid_fase_nueva,$nom_activ,$id_activ);
			    $txt .= '<br>';
			    $txt .= _("puede que tenga que regenerar el proceso");
			    echo $txt;
			    continue;
			}
			$n_ordenSel = $cListaSel[0]->getN_orden();
			foreach($cLista as $oActividadProcesoTarea) {
				$oActividadProcesoTarea->DBCarregar(); // perque tingui tots els valors, y no esborri al grabar.
				$n_orden = $oActividadProcesoTarea->getN_orden();
				$id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
				$id_fase = $oActividadProcesoTarea->getId_fase();
				$id_tarea = $oActividadProcesoTarea->getId_tarea();
				// Relleno las fases intermedias
				if ($n_orden <= $n_ordenSel) {
    				$completado = $oActividadProcesoTarea->getCompletado();
    				if ($completado != 't') {
    				    //buscar of responsable
    				    $GesTareaProcesos = new GestorTareaProceso();
    				    $cTareasProceso = $GesTareaProcesos->getTareasProceso(['id_tipo_proceso'=>$id_tipo_proceso,
    				                                                    'id_fase'=>$id_fase,
    				                                                    'id_tarea'=>$id_tarea
    				                                            ]);
    				    // sólo debería haber uno
    				    if (!empty($cTareasProceso)) {
    				        $oTareaProceso = $cTareasProceso[0];
    				    } else {
    				        $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"),$id_tipo_proceso,$id_fase,$id_tarea);
    				        exit($msg_err);
    				    }
    				    $of_responsable=$oTareaProceso->getOf_responsable();
    				    if (($_SESSION['oPerm']->have_perm_oficna($of_responsable))) {
				            $oActividadProcesoTarea->setCompletado('t');
                            if ($oActividadProcesoTarea->DBGuardar() === false) {
                                echo _("hay un error, no se ha guardado");
                            }
    				    } else {
                            echo _("No tiene permiso para completar la fase, no se ha guardado");
    				    }
    				}
				} elseif ($n_orden > $n_ordenSel) {
                    // Cuando se va hacia atras (pongo sin completar las fases siguientes)
				    $oActividadProcesoTarea->setCompletado('f');
                    if ($oActividadProcesoTarea->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                    }
				}
			}
		}
		break;
	case 'get':
        $Qid_fase_sel = (string) \filter_input(INPUT_POST, 'id_fase_sel');
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ,$Qdl_propia);

		$oGesFases= new GestorActividadFase();
		$oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos,true);
		$oDesplFasesIni->setNombre('id_fase_nueva');
		$oDesplFasesIni->setOpcion_sel($Qid_fase_sel);
		$oDesplFasesIni->setAction('fnjs_lista()');
		$txt = '';
		if (isset($oDesplFasesIni)) {
			$txt .= $oDesplFasesIni->desplegable();
		}
		echo $txt;
		break;
}
