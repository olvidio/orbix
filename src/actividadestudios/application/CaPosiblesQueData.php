<?php

namespace src\actividadestudios\application;

use frontend\shared\config\OrbixRuntime;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Desplegables y texto de grupo para `ca_posibles_que.php`.
 *
 * @return array{
 *   grupo_estudios: ?string,
 *   mi_grupo: string,
 *   aCentrosNExt: array<int|string, string>,
 *   aCentrosAgdExt: array<int|string, string>
 * }
 */
final class CaPosiblesQueData
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   grupo_estudios: ?string,
     *   mi_grupo: string,
     *   aCentrosNExt: array<int|string, string>,
     *   aCentrosAgdExt: array<int|string, string>
     * }
     */
    public function execute(array $input = []): array
    {
        $miDele = OrbixRuntime::miDelef();
        $cMiDl = $this->delegacionRepository->getDelegaciones(['dl' => $miDele]);
        $grupoEstudios = null;
        $miGrupo = '';
        if (count($cMiDl) > 0) {
            $grupoEstudios = $cMiDl[0]->getGrupoEstudiosVo()?->value();
            if ($grupoEstudios !== null) {
                $cDelegaciones = $this->delegacionRepository->getDelegaciones(['grupo_estudios' => $grupoEstudios]);
                foreach ($cDelegaciones as $oDelegacion) {
                    $miGrupo .= $miGrupo === '' ? '' : ',';
                    $miGrupo .= $oDelegacion->getDlVo()->value();
                }
            }
        } else {
            $miGrupo = _('no encuentro el grupo de estudios al que pertenece la dl');
        }

        $aListaCtr = $this->personaNRepository->getArrayIdCentros();
        $aCentrosOrden = [];
        foreach ($aListaCtr as $idUbi) {
            if ($idUbi === null) {
                continue;
            }
            $oCentroDl = $this->centroDlRepository->findById($idUbi);
            if ($oCentroDl === null) {
                continue;
            }
            $nombreUbi = $oCentroDl->getNombre_ubi();
            $aCentrosOrden[$nombreUbi] = [$idUbi => $nombreUbi];
        }
        uksort($aCentrosOrden, 'src\shared\domain\helpers\strsinacentocmp');
        $aCentrosNExt = [];
        $aCentrosNExt[1] = _('todos los ctr');
        $aCentrosNExt[2] = '----------';
        foreach ($aCentrosOrden as $aCentro) {
            $key = key($aCentro);
            $value = current($aCentro);
            $aCentrosNExt[$key] = $value;
        }

        $aListaCtr = $this->personaAgdRepository->getArrayIdCentros();
        $aCentrosOrden = [];
        foreach ($aListaCtr as $idUbi) {
            if ($idUbi === null) {
                continue;
            }
            $oCentroDl = $this->centroDlRepository->findById($idUbi);
            if ($oCentroDl === null) {
                continue;
            }
            $nombreUbi = $oCentroDl->getNombre_ubi();
            $aCentrosOrden[$nombreUbi] = [$idUbi => $nombreUbi];
        }
        uksort($aCentrosOrden, 'src\shared\domain\helpers\strsinacentocmp');
        $aCentrosAgdExt = [];
        $aCentrosAgdExt[1] = _('todos los ctr');
        $aCentrosAgdExt[2] = '----------';
        foreach ($aCentrosOrden as $aCentro) {
            $key = key($aCentro);
            $value = current($aCentro);
            $aCentrosAgdExt[$key] = $value;
        }

        return [
            'grupo_estudios' => $grupoEstudios,
            'mi_grupo' => $miGrupo,
            'aCentrosNExt' => $aCentrosNExt,
            'aCentrosAgdExt' => $aCentrosAgdExt,
        ];
    }
}
