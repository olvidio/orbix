<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenuBits;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: listado estructurado de fases/tareas del proceso.
 */
class ProcesosGetListado
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
        private readonly ActividadTareaRepositoryInterface $actividadTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{a_rows: list<array<string, mixed>>}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_proceso = FuncTablasSupport::inputInt($input, 'id_tipo_proceso');
        $a_status = StatusId::getArrayStatus();

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario?->getId_role() ?? 0;
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aRoles = $this->roleRepository->getArrayRoles();
        $aOpcionesOficinas = PermisoMenuBits::valueToLabel();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            $soy = match ($miSfsv) {
                1 => 1,
                2 => 2,
                default => 0,
            };
        }

        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $Qid_tipo_proceso,
            '_ordre' => 'status,id_of_responsable',
        ]);

        $aRows = [];
        foreach ($cTareasProceso as $oTareaProceso) {
            $id_item = $oTareaProceso->getId_item();
            $status = $oTareaProceso->getStatus();
            $status_txt = $a_status[$status] ?? '';
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $responsable = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];

            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $this->actividadFaseRepository->findById($id_fase);
            if ($oFase === null) {
                continue;
            }
            $fase = $oFase->getDesc_fase();
            $sf = $oFase->isSf() ? 2 : 0;
            $sv = $oFase->isSv() ? 1 : 0;
            if (!(($soy & $sf) || ($soy & $sv))) {
                continue;
            }
            $id_tarea = $oTareaProceso->getId_tarea();
            $oTarea = $this->actividadTareaRepository->findById($id_tarea);
            if ($oTarea === null) {
                continue;
            }
            $tarea = $oTarea->getDesc_tarea();
            $fase_previa = '';
            $aFases_previas = $oTareaProceso->getJsonFasesPreviasAsList();
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'] ?? '';
                if ($id_fase_previa === '' || !is_numeric($id_fase_previa)) {
                    continue;
                }
                $oFase_previa = $this->actividadFaseRepository->findById((int) $id_fase_previa);
                if ($oFase_previa === null) {
                    continue;
                }
                $fase_previa .= $fase_previa === '' ? '' : ' ' . _("y") . ' ';
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
