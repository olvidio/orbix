<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use frontend\shared\web\Periodo;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: datos estructurados para tabla de actividades candidatas a cambiar de fase.
 */
class FasesActivCambioLista
{
    public function __construct(
        private readonly ActividadDlRepositoryInterface $actividadDlRepository,
        private readonly ActividadRepositoryInterface $actividadRepository,
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private readonly ProcesoActividadService $procesoActividadService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $empty = [
            'error' => '',
            'msg' => '',
            'num_activ' => 0,
            'num_ok' => 0,
            'accion' => input_string($input, 'accion'),
            'id_fase_nueva' => input_string($input, 'id_fase_nueva'),
            'a_cabeceras' => [],
            'a_valores' => [],
        ];

        $Qid_tipo_activ = input_string($input, 'id_tipo_activ');
        $Qdl_propia = input_string($input, 'dl_propia');
        $Qid_fase_nueva = input_string($input, 'id_fase_nueva');
        if ($Qid_fase_nueva === '') {
            $empty['error'] = _("Debe poner la fase nueva");
            return $empty;
        }

        $Qperiodo = input_string($input, 'periodo');
        $Qyear = input_string($input, 'year');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');
        $Qaccion = input_string($input, 'accion');

        if ($Qperiodo === '') {
            $Qperiodo = 'actual';
        }

        $aWhere = [];
        $aOperador = [];
        $isfsv = 0;
        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
            $aOperador['id_tipo_activ'] = '~';
            $isfsv = (int)$Qid_tipo_activ[0];
        }
        if (is_true($Qdl_propia)) {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef((string) $isfsv);
            $ActividadRepository = $this->actividadDlRepository;
        } else {
            $aWhere['dl_org'] = ConfigGlobal::mi_delef((string) $isfsv);
            $aOperador['dl_org'] = '!=';
            $ActividadRepository = $this->actividadRepository;
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
        $num_activ = count($cActividades);
        $num_ok = 0;

        $i = 0;
        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $nom_activ = $oActividad->getNom_activ();
            $i++;
            $oTipoDeActiv = $this->tipoDeActividadRepository->findById($id_tipo_activ);
            if ($oTipoDeActiv === null) {
                continue;
            }
            $id_tipo_proceso = $oTipoDeActiv->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            if ($id_tipo_proceso === null) {
                continue;
            }
            $aWhereTP = [
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $Qid_fase_nueva,
            ];
            $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso($aWhereTP);
            if ($cTareasProceso === []) {
                continue;
            }
            $aFases_previas = $cTareasProceso[0]->getJsonFasesPreviasAsList();

            $aFases_estado = $this->actividadProcesoTareaRepository->getListaFaseEstado($id_activ);
            $aFases_completadas = $this->actividadProcesoTareaRepository->getFasesCompletadas($id_activ);

            $mensaje = '';
            $ok_fases_previas = false;
            if ($Qaccion === 'desmarcar') {
                $mensaje = _("No tiene marcada la fase");
                if (in_array((int) $Qid_fase_nueva, $aFases_completadas, true)) {
                    $ok_fases_previas = true;
                    $mensaje = 'ok';
                }
            } else {
                if ($aFases_estado === []) {
                    $mensaje = _("No tiene proceso. Debe crearlo");
                    $a_valores[$i]['clase'] = 'wrong-soft';
                } else {
                    if (in_array((int) $Qid_fase_nueva, $aFases_completadas, true)) {
                        $mensaje = _("Ya la tiene");
                    } else {
                        if ($aFases_previas !== []) {
                            $ok_fases_previas = true;
                            foreach ($aFases_previas as $aaFase_previa) {
                                $id_fase_previa_raw = $aaFase_previa['id_fase'] ?? '';
                                if ($id_fase_previa_raw === '' || !is_numeric($id_fase_previa_raw)) {
                                    continue;
                                }
                                $id_fase_previa = (int) $id_fase_previa_raw;
                                $mensaje_requisitoRaw = $aaFase_previa['mensaje'] ?? '';
                                $mensaje_requisito = is_string($mensaje_requisitoRaw) ? $mensaje_requisitoRaw : '';
                                if ($id_fase_previa > 0 && in_array($id_fase_previa, $aFases_completadas, true)) {
                                    $mensaje = _("ok, tiene la(s) fase(s) previa(s)");
                                } else {
                                    $ok_fases_previas = false;
                                    $fase_tarea_previa = $id_fase_previa . '#0';
                                    $mensaje .= $mensaje_requisito === ''
                                        ? $this->procesoActividadService->getMensaje($fase_tarea_previa, 'marcar')
                                        : $mensaje_requisito;
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
