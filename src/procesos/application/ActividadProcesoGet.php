<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function core\is_true;

/**
 * Caso de uso: devuelve las tareas del proceso para un id_activ como
 * estructura (completado, fase, tarea, responsable, observ) + flag de
 * permiso de edicion. El render HTML se hace en el frontend.
 */
class ActividadProcesoGet
{
    public function execute(array $input): array
    {
        $Qid_activ = (int)($input['id_activ'] ?? 0);

        $aWhere = [
            'id_activ' => $Qid_activ,
            '_ordre' => 'id_fase',
        ];
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $oLista = $ActividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);

        $aRows = [];
        foreach ($oLista as $oActividadProcesoTarea) {
            $id_item = (int)$oActividadProcesoTarea->getId_item();
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            $completado = is_true($oActividadProcesoTarea->isCompletado());
            $observ = (string)$oActividadProcesoTarea->getObserv();

            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            if (empty($fase)) {
                continue;
            }
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();

            $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea,
            ]);
            if (empty($cTareasProceso)) {
                return [
                    'error' => sprintf(
                        _("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"),
                        $id_tipo_proceso,
                        $id_fase,
                        $id_tarea
                    ),
                    'a_rows' => [],
                ];
            }
            $oTareaProceso = $cTareasProceso[0];
            $of_responsable_txt = (string)$oTareaProceso->getOf_responsable_txt();
            $puede_editar = empty($of_responsable_txt)
                || ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt));

            $aRows[] = [
                'id_item' => $id_item,
                'fase' => $fase,
                'tarea' => $tarea,
                'of_responsable_txt' => $of_responsable_txt,
                'completado' => $completado,
                'observ' => $observ,
                'puede_editar' => $puede_editar,
            ];
        }

        return [
            'error' => '',
            'a_rows' => $aRows,
        ];
    }
}
