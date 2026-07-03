<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\permisos\domain\XPermisos;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: tareas del proceso para un id_activ (estructura + permiso edición).
 */
class ActividadProcesoGet
{
    public function __construct(
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
        private readonly ActividadTareaRepositoryInterface $actividadTareaRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string, a_rows: list<array<string, mixed>>}
     */
    public function execute(array $input): array
    {
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');

        $aWhere = [
            'id_activ' => $Qid_activ,
            '_ordre' => 'id_fase',
        ];
        $oLista = $this->actividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);

        $aRows = [];
        foreach ($oLista as $oActividadProcesoTarea) {
            $id_item = (int)$oActividadProcesoTarea->getId_item();
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            if ($id_fase === null || $id_tarea === null) {
                continue;
            }
            $completado = \src\shared\domain\helpers\FuncTablasSupport::isTrue($oActividadProcesoTarea->isCompletado());
            $observ = (string)$oActividadProcesoTarea->getObserv();

            $oFase = $this->actividadFaseRepository->findById($id_fase);
            if ($oFase === null) {
                continue;
            }
            $fase = $oFase->getDesc_fase();
            if ($fase === '') {
                continue;
            }
            $oTarea = $this->actividadTareaRepository->findById($id_tarea);
            if ($oTarea === null) {
                continue;
            }
            $tarea = $oTarea->getDesc_tarea();

            $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso([
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea,
            ]);
            if ($cTareasProceso === []) {
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
            $oPerm = $_SESSION['oPerm'] ?? null;
            $puede_editar = $of_responsable_txt === ''
                || ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina($of_responsable_txt));

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
