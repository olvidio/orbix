<?php

namespace src\procesos\application;

use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `procesos_ver` (formulario
 * editar / nuevo de una fase dentro de un tipo de proceso).
 *
 * Devuelve todos los arrays necesarios para que el controlador
 * frontend monte los `frontend\shared\web\Desplegable` (fases, tareas, status,
 * oficinas responsables, fases previas y sus tareas) y el
 * formulario de edicion.
 */
class ProcesosVerData
{
    /**
     * @param string $mod 'editar' o 'nuevo'
     * @param int $id_item id de la fila TareaProceso si mod=editar
     * @return array
     */
    public static function execute(string $mod, int $id_item): array
    {
        $oPermMenus = new PermisoMenu();
        $a_oficinas = $oPermMenus->lista_array();

        $a_status = StatusId::getArrayStatus();

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $a_fases = $ActividadFaseRepository->getArrayActividadFases();

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
            $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
            $oTareaProceso = $TareaProcesoRepository->findById($id_item);
            $data['status'] = $oTareaProceso->getStatus();
            $data['id_of_responsable'] = $oTareaProceso->getId_of_responsable();
            $data['id_fase'] = $oTareaProceso->getId_fase();
            $data['id_tarea'] = $oTareaProceso->getId_tarea();
            $data['a_tareas'] = $ActividadTareaRepository->getArrayActividadTareas($data['id_fase']);

            $aFases_previas = $oTareaProceso->getJson_fases_previas(true);
            $a_previas = [];
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'] ?? '';
                if (empty($id_fase_previa)) {
                    continue;
                }
                $a_previas[] = [
                    'id_fase_previa' => $id_fase_previa,
                    'id_tarea_previa' => $oFaseP['id_tarea'] ?? '',
                    'mensaje_requisito' => $oFaseP['mensaje'] ?? '',
                    'a_tareas_previa' => $ActividadTareaRepository->getArrayActividadTareas($id_fase_previa),
                ];
            }
            if (empty($a_previas)) {
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
