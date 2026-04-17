<?php

/*
 * DEPRECADO: dispatcher con parametro `que` heredado de
 * apps/procesos/controller/fases_activ_cambio_ajax.php. Se mantiene como
 * wrapper temporal hasta refactorizar por accion (`lista`, `update`, `get`).
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Lista;
use web\Periodo;
use web\Posicion;
use function core\is_true;

header('Content-Type: text/plain; charset=UTF-8');

$oPosicion = new Posicion($_SERVER['PHP_SELF'], $_POST);

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');

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

        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }

        $refresh = $oPosicion->getParametro('refresh', 1);
        if (empty($refresh)) {
            $oPosicion->recordar();
            $refresh = 1;
        }
        $aGoBack = [
            'refresh' => $refresh,
            'hnov' => 0,
            'que' => $Qque,
            'dl_propia' => $Qdl_propia,
            'id_fase_nueva' => $Qid_fase_nueva,
            'id_tipo_activ' => $Qid_tipo_activ,
            'periodo' => $Qperiodo,
            'year' => $Qyear,
            'empiezamin' => $Qempiezamin,
            'empiezamax' => $Qempiezamax,
            'accion' => $Qaccion,
        ];
        $oPosicion->setParametros($aGoBack, 1);

        $aWhere = [];
        $aOperador = [];
        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
            $isfsv = (int)$Qid_tipo_activ[0];
        }
        if (is_true($Qdl_propia)) {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef($isfsv);
            $ActividadRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        } else {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef($isfsv);
            $aOperador['dl_org'] = '!=';
            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        }
        $aWhere['status'] = 4;
        $aOperador['status'] = '<';

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        if (!empty($Qperiodo) && $Qperiodo === 'desdeHoy') {
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

        if ($Qaccion === 'desmarcar') {
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
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
        if (!is_array($cActividades)) {
            exit(_("faltan condiciones para la selección"));
        }
        $num_activ = count($cActividades);
        $num_ok = 0;
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ProcesoActividadService = $GLOBALS['container']->get(ProcesoActividadService::class);
        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $nom_activ = $oActividad->getNom_activ();
            $i++;
            $oTipoDeActiv = $TipoDeActividadRepository->findById($id_tipo_activ);
            $id_tipo_proceso = $oTipoDeActiv->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $aWhereTP = [
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $Qid_fase_nueva,
            ];
            $cTareasProceso = $TareaProcesoRepository->getTareasProceso($aWhereTP);
            $aFases_previas = $cTareasProceso[0]->getJson_fases_previas(true);

            $aFases_estado = $ActividadProcesoTareaRepository->getListaFaseEstado($id_activ);
            $aFases_completadas = $ActividadProcesoTareaRepository->getFasesCompletadas($id_activ);

            $mensaje = '';
            if ($Qaccion === 'desmarcar') {
                $ok_fases_previas = false;
                $mensaje = _("No tiene marcada la fase");
                if (in_array($Qid_fase_nueva, $aFases_completadas)) {
                    $ok_fases_previas = true;
                    $mensaje = 'ok';
                }
            } else {
                if (empty($aFases_estado)) {
                    $ok_fases_previas = false;
                    $mensaje = _("No tiene proceso. Debe crearlo");
                    $a_valores[$i]['clase'] = 'wrong-soft';
                } else {
                    if (in_array($Qid_fase_nueva, $aFases_completadas)) {
                        $ok_fases_previas = false;
                        $mensaje = _("Ya la tiene");
                    } else {
                        if (!empty($aFases_previas)) {
                            $ok_fases_previas = true;
                            foreach ($aFases_previas as $aaFase_previa) {
                                $id_fase_previa = $aaFase_previa['id_fase'];
                                $mensaje_requisito = $aaFase_previa['mensaje'];
                                if (in_array($id_fase_previa, $aFases_completadas)) {
                                    $mensaje = _("ok, tiene la(s) fase(s) previa(s)");
                                } else {
                                    $ok_fases_previas = false;
                                    $fase_tarea_previa = $id_fase_previa . '#0';
                                    $mensaje .= empty($mensaje_requisito) ? $ProcesoActividadService->getMensaje($fase_tarea_previa, 'marcar') : $mensaje_requisito;
                                }
                            }
                        } else {
                            $ok_fases_previas = true;
                            $mensaje = 'ok';
                        }
                    }
                }
            }

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
        $a_camposHidden = [
            'id_fase_nueva' => $Qid_fase_nueva,
            'que' => 'update',
            'accion' => $Qaccion,
        ];
        $oHash->setArraycamposHidden($a_camposHidden);

        $msg = sprintf(_("%s actividades, %s para cambiar"), $num_activ, $num_ok);

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

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        foreach ($a_sel as $id_activ) {
            $id_activ = strtok($id_activ, "#");
            $cListaSel = $ActividadProcesoTareaRepository->getActividadProcesoTareas(['id_activ' => $id_activ, 'id_fase' => $Qid_fase_nueva]);
            if (empty($cListaSel)) {
                $oActividad = $ActividadAllRepository->findById($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $txt = sprintf(_("No se encuentra esta fase %s para esta actividad %s(%s)"), $Qid_fase_nueva, $nom_activ, $id_activ);
                $txt .= '<br>';
                $txt .= _("puede que tenga que regenerar el proceso");
                echo $txt;
                continue;
            }
            $oActividadProcesoTarea = $cListaSel[0];
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea,
            ]);
            if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
            } else {
                $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
                exit($msg_err);
            }
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
            if (empty($of_responsable_txt) || $_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
                if ($Qaccion === 'desmarcar') {
                    $oActividadProcesoTarea->setCompletado('f');
                } else {
                    $oActividadProcesoTarea->setCompletado('t');
                }
                $ProcesoActividadService = $GLOBALS['container']->get(ProcesoActividadService::class);
                if ($ProcesoActividadService->guardar($oActividadProcesoTarea) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $ActividadProcesoTareaRepository->getErrorTxt();
                }
            } else {
                echo _("No tiene permiso para completar la fase, no se ha guardado");
            }
        }
        break;

    case 'get':
        $Qid_fase_sel = (string)filter_input(INPUT_POST, 'id_fase_sel');
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aOpciones = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos, true);
        $oDesplFasesIni = new Desplegable();
        $oDesplFasesIni->setBlanco(true);
        $oDesplFasesIni->setOpciones($aOpciones);
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
