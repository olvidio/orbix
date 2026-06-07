<?php

namespace src\procesos\application;

use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenuBits;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `procesos_ver`.
 */
class ProcesosVerData
{
    public function __construct(
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
        private readonly ActividadTareaRepositoryInterface $actividadTareaRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $mod, int $id_item): array
    {
        $a_oficinas = PermisoMenuBits::valueToLabel();
        $a_status = StatusId::getArrayStatus();
        $a_fases = $this->actividadFaseRepository->getArrayActividadFases();

        $data = [
            'mod' => $mod,
            'a_oficinas' => $a_oficinas,
            'a_status' => $a_status,
            'a_fases' => $a_fases,
            'a_tareas' => [],
            'status' => '',
            'id_of_responsable' => '',
            'id_fase' => '',
            'id_tarea' => '',
            'a_fases_previas' => [],
        ];

        if ($mod === 'editar') {
            $oTareaProceso = $this->tareaProcesoRepository->findById($id_item);
            if ($oTareaProceso === null) {
                return $data;
            }
            $data['status'] = $oTareaProceso->getStatus();
            $data['id_of_responsable'] = $oTareaProceso->getId_of_responsable();
            $data['id_fase'] = $oTareaProceso->getId_fase();
            $data['id_tarea'] = $oTareaProceso->getId_tarea();
            $data['a_tareas'] = $this->actividadTareaRepository->getArrayActividadTareas($data['id_fase']);

            $aFases_previas = $oTareaProceso->getJsonFasesPreviasAsList();
            $a_previas = [];
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'] ?? '';
                if ($id_fase_previa === '' || !is_numeric($id_fase_previa)) {
                    continue;
                }
                $idFasePrevia = (int) $id_fase_previa;
                $a_previas[] = [
                    'id_fase_previa' => $id_fase_previa,
                    'id_tarea_previa' => $oFaseP['id_tarea'] ?? '',
                    'mensaje_requisito' => $oFaseP['mensaje'] ?? '',
                    'a_tareas_previa' => $this->actividadTareaRepository->getArrayActividadTareas($idFasePrevia),
                ];
            }
            if ($a_previas === []) {
                $a_previas[] = [
                    'id_fase_previa' => '',
                    'id_tarea_previa' => '',
                    'mensaje_requisito' => '',
                    'a_tareas_previa' => [],
                ];
            }
            $data['a_fases_previas'] = $a_previas;
        } else {
            $data['a_fases_previas'] = [[
                'id_fase_previa' => '',
                'id_tarea_previa' => '',
                'mensaje_requisito' => '',
                'a_tareas_previa' => [],
            ]];
        }

        return $data;
    }
}
