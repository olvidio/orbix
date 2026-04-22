<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use web\Periodo;
use web\Posicion;
use function core\is_true;

/**
 * Caso de uso: devuelve los datos estructurados para la tabla de
 * actividades candidatas a cambiar de fase, segun filtros de tipo de
 * actividad, dl_propia, periodo y accion (marcar/desmarcar).
 *
 * El frontend renderiza el formulario con `web\Lista` + `web\Hash`.
 */
class FasesActivCambioLista
{
    /**
     * @return array{
     *     error:string,
     *     msg:string,
     *     num_activ:int,
     *     num_ok:int,
     *     accion:string,
     *     id_fase_nueva:string,
     *     a_cabeceras:array<int,string>,
     *     a_valores:array<mixed>
     * }
     */
    public function execute(array $input): array
    {
        $empty = [
            'error' => '',
            'msg' => '',
            'num_activ' => 0,
            'num_ok' => 0,
            'accion' => (string)($input['accion'] ?? ''),
            'id_fase_nueva' => (string)($input['id_fase_nueva'] ?? ''),
            'a_cabeceras' => [],
            'a_valores' => [],
        ];

        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qdl_propia = (string)($input['dl_propia'] ?? '');
        $Qid_fase_nueva = (string)($input['id_fase_nueva'] ?? '');
        if (empty($Qid_fase_nueva)) {
            $empty['error'] = _("Debe poner la fase nueva");
            return $empty;
        }

        $Qperiodo = (string)($input['periodo'] ?? '');
        $Qyear = (string)($input['year'] ?? '');
        $Qempiezamin = (string)($input['empiezamin'] ?? '');
        $Qempiezamax = (string)($input['empiezamax'] ?? '');
        $Qaccion = (string)($input['accion'] ?? '');

        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }

        $oPosicion = new Posicion($_SERVER['PHP_SELF'], $input);
        $refresh = $oPosicion->getParametro('refresh', 1);
        if (empty($refresh)) {
            $oPosicion->recordar();
            $refresh = 1;
        }
        $aGoBack = [
            'refresh' => $refresh,
            'hnov' => 0,
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
        $isfsv = 0;
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
        if ($Qperiodo === 'desdeHoy') {
            $aWhere['f_fin'] = "'$inicioIso','$finIso'";
            $aOperador['f_fin'] = 'BETWEEN';
        } else {
            $aWhere['f_ini'] = "'$inicioIso','$finIso'";
            $aOperador['f_ini'] = 'BETWEEN';
        }

        $a_cabeceras = [_("nom"), _("cumple requisito")];

        $a_valores = [];
        $aWhere['_ordre'] = 'f_ini';
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
        if (!is_array($cActividades)) {
            $empty['error'] = _("faltan condiciones para la selección");
            return $empty;
        }
        $num_activ = count($cActividades);
        $num_ok = 0;

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ProcesoActividadService = $GLOBALS['container']->get(ProcesoActividadService::class);

        $i = 0;
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
            $ok_fases_previas = false;
            if ($Qaccion === 'desmarcar') {
                $mensaje = _("No tiene marcada la fase");
                if (in_array($Qid_fase_nueva, $aFases_completadas)) {
                    $ok_fases_previas = true;
                    $mensaje = 'ok';
                }
            } else {
                if (empty($aFases_estado)) {
                    $mensaje = _("No tiene proceso. Debe crearlo");
                    $a_valores[$i]['clase'] = 'wrong-soft';
                } else {
                    if (in_array($Qid_fase_nueva, $aFases_completadas)) {
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

        $msg = sprintf(_("%s actividades, %s para cambiar"), $num_activ, $num_ok);

        return [
            'error' => '',
            'msg' => $msg,
            'num_activ' => $num_activ,
            'num_ok' => $num_ok,
            'accion' => $Qaccion,
            'id_fase_nueva' => $Qid_fase_nueva,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
