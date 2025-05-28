<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividades\model\entity\GestorActividadDl;
use actividades\model\entity\GestorTipoDeActividad;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\ActividadProcesoTarea;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorTareaProceso;
use web\Hash;
use web\Lista;
use web\Periodo;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
//$Qid_tipo_proceso = (integer) filter_input(INPUT_POST, 'id_tipo_proceso');

switch ($Qque) {
    case 'lista':
        $Qid_fase_nueva = (string)filter_input(INPUT_POST, 'id_fase_nueva');
        if (empty($Qid_fase_nueva)) {
            exit('<h2>' . _("Debe poner la fase nueva") . '</h2>');
        }

        $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
        $Qyear = (string)filter_input(INPUT_POST, 'year');
        $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
        $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
        $Qaccion = (string)filter_input(INPUT_POST, 'accion');

        // valores por defeccto
        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }

        $refresh = $oPosicion->getParametro('refresh', 1);
        if (empty($refresh)) {
            $oPosicion->recordar();
            $refresh = 1;
        }
        $aGoBack = array(
            'refresh' => $refresh,
            'hnov' => 0, // Como vengo de un liknkSinVal, está a 1, y no se borra.
            'que' => $Qque,
            'dl_propia' => $Qdl_propia,
            'id_fase_nueva' => $Qid_fase_nueva,
            'id_tipo_activ' => $Qid_tipo_activ,
            'periodo' => $Qperiodo,
            'year' => $Qyear,
            'empiezamin' => $Qempiezamin,
            'empiezamax' => $Qempiezamax,
            'accion' => $Qaccion,
        );
        $oPosicion->setParametros($aGoBack, 1);

        $aWhere = [];
        $aOperador = [];
        // id_tipo_activ
        if ($Qid_tipo_activ != '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
            $isfsv = (integer)substr($Qid_tipo_activ, 0, 1);
        }
        // dl_org
        if (is_true($Qdl_propia)) {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef($isfsv);
            $gesActividades = new GestorActividadDl();
        } else {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef($isfsv);
            $aOperador['dl_org'] = '!=';
            $gesActividades = new GestorActividad();
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

        $i = 0;
        $a_cabeceras = [];
        $a_cabeceras[] = _("nom");
        $a_cabeceras[] = _("cumple requisito");

        if ($Qaccion == 'desmarcar') {
            $txt_cambiar = _("descambiar los marcados");
        } else {
            $txt_cambiar = _("cambiar los marcados");
        }
        $a_botones = [
            ['txt' => $txt_cambiar, 'click' => "fnjs_cambiar(\"#seleccionados\")"],
            ['txt' => _("ver proceso actividad"), 'click' => "fnjs_ver_activ(\"#seleccionados\")"],
            ['txt' => _("todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)"],
            ['txt' => _("ninguno"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)"],
            ['txt' => _("invertir"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\",0)"],
        ];


        $a_valores = [];
        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $gesActividades->getActividades($aWhere, $aOperador);
        if (!is_array($cActividades)) {
            exit (_("faltan condiciones para la selección"));
        }
        $num_activ = count($cActividades);
        $num_ok = 0;
        foreach ($cActividades as $oActividad) {
            //print_r($oActividad);
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $nom_activ = $oActividad->getNom_activ();
            $i++;
            // Por el tipo de actividad sé el tipo de proceso
            $oTipoActiv = new TipoDeActividad(array('id_tipo_activ' => $id_tipo_activ));
            $id_tipo_proceso = $oTipoActiv->getId_tipo_proceso();
            $aWhereTP = [
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $Qid_fase_nueva,
            ];
            $GesTareaProcesos = new GestorTareaProceso();
            $cTareasProceso = $GesTareaProcesos->getTareasProceso($aWhereTP);
            $aFases_previas = $cTareasProceso[0]->getJson_fases_previas(TRUE);

            // Busco el proceso de esta actividad. Las fases completadas.
            $GesActivProceso = new GestorActividadProcesoTarea();
            $aFases_estado = $GesActivProceso->getListaFaseEstado($id_activ);
            $aFases_completadas = $GesActivProceso->getFasesCompletadas($id_activ);

            $mensaje = '';
            if ($Qaccion == 'desmarcar') {
                // para desmarcar solo miro si está marcada:
                $ok_fases_previas = FALSE;
                $mensaje = _("No tiene marcada la fase");
                if (in_array($Qid_fase_nueva, $aFases_completadas)) {
                    $ok_fases_previas = TRUE;
                    $mensaje = 'ok';
                }
            } else {
                if (empty($aFases_estado)) {
                    // 1.- No tiene proceso. 
                    $ok_fases_previas = FALSE;
                    $mensaje = _("No tiene proceso. Debe crearlo");
                    $a_valores[$i]['clase'] = 'wrong-soft';
                } else {
                    // 2.- ya la tiene completada
                    if (in_array($Qid_fase_nueva, $aFases_completadas)) {
                        $ok_fases_previas = FALSE;
                        $mensaje = _("Ya la tiene");
                    } else {
                        // miro si tiene la fase requerida.
                        if (!empty($aFases_previas)) {
                            $ok_fases_previas = TRUE;
                            foreach ($aFases_previas as $aaFase_previa) {
                                $id_fase_previa = $aaFase_previa['id_fase'];
                                $mensaje_requisito = $aaFase_previa['mensaje'];
                                if (in_array($id_fase_previa, $aFases_completadas)) {
                                    // 3.- Si tiene la fase requerida
                                    $mensaje = _("ok, tiene la(s) fase(s) previa(s)");
                                } else {
                                    // 4.- Falta por lo menos una fase requerida
                                    $ok_fases_previas = FALSE;
                                    $oActividadProcesoTarea = new ActividadProcesoTarea();
                                    $fase_tarea_previa = $id_fase_previa . '#0';
                                    $mensaje .= empty($mensaje_requisito) ? $oActividadProcesoTarea->getMensaje($fase_tarea_previa, 'marcar') : $mensaje_requisito;
                                }
                            }
                        } else {
                            // 5.- No requiere fases previas, ok requisito.
                            $ok_fases_previas = TRUE;
                            $mensaje = 'ok';
                        }
                    }
                }
            }

            // mostrar lista
            if ($ok_fases_previas) {
                $a_valores['select'][] = $id_activ;
                $num_ok++;
            }
            $a_valores[$i]['sel'] = $id_activ;
            $a_valores[$i][1] = $nom_activ;
            $a_valores[$i][2] = $mensaje;

        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('actividades_fases_cambio_ajax');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);

        $oHash = new Hash();
        $oHash->setCamposForm('sel');
        $oHash->setcamposNo('scroll_id');
        $a_camposHidden = array(
            'id_fase_nueva' => $Qid_fase_nueva,
            'que' => 'update',
            'accion' => $Qaccion,
        );
        $oHash->setArraycamposHidden($a_camposHidden);

        $msg = sprintf(_("%s actividades, %s para cambiar"), $num_activ, $num_ok);

        // Hay que ver la tabla en Html, porque conslickgrid sólo se seleccionan los visibles y
        // normalmente queremos todos.
        $oTabla->setFormatoTabla('html');

        $txt = '<form id="seleccionados" name="seleccionados" action="" method="post">';
        $txt .= $oHash->getCamposHtml();
        $txt .= $oTabla->mostrar_tabla();
        $txt .= '</form>';

        echo $msg;
        echo $txt;
        break;
    case 'update':
        $Qid_fase_nueva = (string)filter_input(INPUT_POST, 'id_fase_nueva');
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qaccion = (string)filter_input(INPUT_POST, 'accion');

        foreach ($a_sel as $id_activ) {
            $id_activ = strtok($id_activ, "#");
            $gesActividadProcesoTareas = new GestorActividadProcesoTarea();
            // selecciono todas las tareas de esta fase.
            $cListaSel = $gesActividadProcesoTareas->getActividadProcesoTareas(array('id_activ' => $id_activ, 'id_fase' => $Qid_fase_nueva));
            if (empty($cListaSel)) {
                // No se encuentra esta fase para esta actividad
                $oActividad = new ActividadAll($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $txt = sprintf(_("No se encuentra esta fase %s para esta actividad %s(%s)"), $Qid_fase_nueva, $nom_activ, $id_activ);
                $txt .= '<br>';
                $txt .= _("puede que tenga que regenerar el proceso");
                echo $txt;
                continue;
            }
            $oActividadProcesoTarea = $cListaSel[0];
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            //buscar of responsable
            $GesTareaProcesos = new GestorTareaProceso();
            $cTareasProceso = $GesTareaProcesos->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea
            ]);
            // sólo debería haber uno
            if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
            } else {
                $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
                exit($msg_err);
            }
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
            if (empty($of_responsable_txt) || $_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
                if ($Qaccion == 'desmarcar') {
                    $oActividadProcesoTarea->setCompletado('f');
                } else {
                    $oActividadProcesoTarea->setCompletado('t');
                }
                if ($oActividadProcesoTarea->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oActividadProcesoTarea->getErrorTxt();
                }
            } else {
                echo _("No tiene permiso para completar la fase, no se ha guardado");
            }
        }
        break;
    case 'get':
        $Qid_fase_sel = (string)filter_input(INPUT_POST, 'id_fase_sel');
        // buscar los procesos posibles para estos tipos de actividad
        $GesTiposActiv = new GestorTipoDeActividad();
        $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);

        $oGesFases = new GestorActividadFase();
        $oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos, true);
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
