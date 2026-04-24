<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso: devuelve el listado (estructurado) de fases/tareas del
 * proceso filtrando por sfsv/role. El render HTML se hace en el frontend.
 */
class ProcesosGetListado
{
    public function execute(array $input): array
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);
        $a_status = StatusId::getArrayStatus();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $oPermMenus = new PermisoMenu();
        $aOpcionesOficinas = $oPermMenus->lista_array();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            $soy = 0;
            switch ($miSfsv) {
                case 1:
                    $soy = 1;
                    break;
                case 2:
                    $soy = 2;
                    break;
            }
        }

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $Qid_tipo_proceso,
            '_ordre' => 'status,id_of_responsable',
        ]);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);

        $aRows = [];
        foreach ($cTareasProceso as $oTareaProceso) {
            $id_item = $oTareaProceso->getId_item();
            $status = $oTareaProceso->getStatus();
            $status_txt = $a_status[$status] ?? '';
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $responsable = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];

            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            $sf = ($oFase->isSf()) ? 2 : 0;
            $sv = ($oFase->isSv()) ? 1 : 0;
            if (!(($soy & $sf) || ($soy & $sv))) {
                continue;
            }
            $id_tarea = $oTareaProceso->getId_tarea();
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();
            $fase_previa = '';
            $aFases_previas = $oTareaProceso->getJson_fases_previas(true);
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'];
                if (empty($id_fase_previa)) {
                    continue;
                }
                $oFase_previa = $ActividadFaseRepository->findById($id_fase_previa);
                $fase_previa .= empty($fase_previa) ? '' : ' ' . _("y") . ' ';
                $fase_previa .= $oFase_previa->getDesc_fase();
            }

            $aRows[] = [
                'id_item' => (int)$id_item,
                'status_txt' => $status_txt,
                'responsable' => $responsable,
                'fase' => $fase,
                'tarea' => $tarea,
                'fase_previa' => $fase_previa,
            ];
        }

        return ['a_rows' => $aRows];
    }
}
