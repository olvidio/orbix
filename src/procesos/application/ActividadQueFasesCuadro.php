<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

/**
 * Genera el HTML de un bloque de checkboxes de fases (fases_on o fases_off)
 * para el filtro de busqueda de actividades.
 */
final class ActividadQueFasesCuadro
{
    /**
     * @param string $id_tipo_activ patron del tipo de actividad (p.e. "1...." / "1a..").
     * @param bool $dl_propia true si el filtro se hace contra la propia delegacion.
     * @param string $name nombre del input (`fases_on` o `fases_off`).
     * @param int[] $selected ids de fase que deben salir con `checked`.
     */
    public function ejecutar(string $id_tipo_activ, bool $dl_propia, string $name, array $selected): string
    {
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aFases = $ActividadFaseRepository->getArrayFasesProcesos($aTiposDeProcesos);

        $html = '';
        foreach ($aFases as $descripcion => $id_fase) {
            $chk = in_array((int)$id_fase, $selected, true) ? ' checked' : '';
            $html .= "<input type='checkbox' name='{$name}[]' value='{$id_fase}'{$chk} /> {$descripcion}";
        }
        return $html;
    }
}
