<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use function src\shared\domain\helpers\input_int;

/**
 * Caso de uso: estructura padres/hijos del árbol de fases del proceso.
 */
class ProcesosGet
{
    public function __construct(
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{aPadres: array<int, array<int, array{id: int, nom: string}>>}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_proceso = input_int($input, 'id_tipo_proceso');

        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $Qid_tipo_proceso,
            '_ordre' => 'status,id_of_responsable',
        ]);

        StatusId::getArrayStatus();

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario?->getId_role() ?? 0;
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aRoles = $this->roleRepository->getArrayRoles();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            $soy = match ($miSfsv) {
                1 => 1,
                2 => 2,
                default => 0,
            };
        }

        $j = 0;
        /** @var array<int, array<int, array{id: int, nom: string}>> $aPadres */
        $aPadres = [];
        foreach ($cTareasProceso as $oTareaProceso) {
            $j++;
            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $this->actividadFaseRepository->findById($id_fase);
            if ($oFase === null) {
                $j--;
                continue;
            }
            $fase = $oFase->getDesc_fase();
            $sf = $oFase->isSf() ? 2 : 0;
            $sv = $oFase->isSv() ? 1 : 0;
            if (!(($soy & $sf) || ($soy & $sv))) {
                $j--;
                continue;
            }
            $aFases_previas = $oTareaProceso->getJsonFasesPreviasAsList();
            $id_fase_previa = 0;
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa_raw = $oFaseP['id_fase'] ?? '';
                if ($id_fase_previa_raw === '' || !is_numeric($id_fase_previa_raw)) {
                    continue;
                }
                $id_fase_previa = (int) $id_fase_previa_raw;
            }
            $aPadres[$id_fase_previa][$j] = ['id' => (int) $id_fase, 'nom' => (string) ($fase ?? '')];
        }

        return ['aPadres' => $aPadres];
    }
}
