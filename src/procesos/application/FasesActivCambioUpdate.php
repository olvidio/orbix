<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: aplica setCompletado(t|f) a la tarea de la fase nueva para
 * cada id_activ seleccionado, respetando permisos de oficina del responsable.
 */
class FasesActivCambioUpdate
{
    public function execute(array $input): string
    {
        $Qid_fase_nueva = (string)($input['id_fase_nueva'] ?? '');
        $a_sel = (array)($input['sel'] ?? []);
        $Qaccion = (string)($input['accion'] ?? '');

        $txtOut = '';

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        foreach ($a_sel as $id_activ) {
            $id_activ = strtok($id_activ, "#");
            $cListaSel = $ActividadProcesoTareaRepository->getActividadProcesoTareas([
                'id_activ' => $id_activ,
                'id_fase' => $Qid_fase_nueva,
            ]);
            if (empty($cListaSel)) {
                $oActividad = $ActividadAllRepository->findById($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $txt = sprintf(_("No se encuentra esta fase %s para esta actividad %s(%s)"), $Qid_fase_nueva, $nom_activ, $id_activ);
                $txt .= '<br>';
                $txt .= _("puede que tenga que regenerar el proceso");
                $txtOut .= $txt;
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
                return sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
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
                    $err = $ProcesoActividadService->getErrorTxt();
                    if ($err !== '') {
                        $txtOut .= $err;
                    } else {
                        $txtOut .= _("hay un error, no se ha guardado");
                        $txtOut .= "\n" . $ActividadProcesoTareaRepository->getErrorTxt();
                    }
                    $txtOut .= '<br>';
                }
            } else {
                $txtOut .= _("No tiene permiso para completar la fase, no se ha guardado");
            }
        }

        return $txtOut;
    }
}
