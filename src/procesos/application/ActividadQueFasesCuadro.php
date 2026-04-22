<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

/**
 * Caso de uso: devuelve la lista de fases aplicables al tipo de actividad
 * indicado (estructura pura) para construir los checkboxes de `fases_on`
 * o `fases_off` del filtro de busqueda de actividades.
 */
final class ActividadQueFasesCuadro
{
    /**
     * @param string $id_tipo_activ patron del tipo de actividad (p.e. "1...." / "1a..").
     * @param bool $dl_propia true si el filtro se hace contra la propia delegacion.
     * @param int[] $selected ids de fase que deben salir con checked.
     * @return array{a_fases: array<array{id:int,nom:string,checked:bool}>}
     */
    public function ejecutar(string $id_tipo_activ, bool $dl_propia, array $selected): array
    {
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aFases = $ActividadFaseRepository->getArrayFasesProcesos($aTiposDeProcesos);

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
