<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

/**
 * Caso de uso: fases aplicables al tipo de actividad para checkboxes fases_on/fases_off.
 */
final class ActividadQueFasesCuadro
{
    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param int[] $selected
     * @return array{a_fases: list<array{id: int, nom: string, checked: bool}>}
     */
    public function ejecutar(string $id_tipo_activ, bool $dl_propia, array $selected): array
    {
        $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
        $aFases = $this->actividadFaseRepository->getArrayFasesProcesos($aTiposDeProcesos);

        $aOut = [];
        foreach ($aFases as $descripcion => $id_fase) {
            $aOut[] = [
                'id' => (int)$id_fase,
                'nom' => (string)$descripcion,
                'checked' => in_array((int)$id_fase, $selected, true),
            ];
        }

        return ['a_fases' => $aOut];
    }
}
