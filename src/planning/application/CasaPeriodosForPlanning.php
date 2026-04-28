<?php

namespace src\planning\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

/**
 * Precarga periodos sf/sv/res por id_ubi para el planning de casas (claves u#…).
 */
final class CasaPeriodosForPlanning
{
    /**
     * @param array<string, array<int, array<string, array>>> $a_actividades
     * @return array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>
     */
    public static function collect(array $a_actividades, DateTimeLocal $oIni, DateTimeLocal $oFin): array
    {
        $ids = [];
        foreach ($a_actividades as $ww) {
            foreach ($ww as $per => $_) {
                $parts = explode('#', (string)$per, 3);
                if (count($parts) >= 2 && $parts[0] === 'u' && $parts[1] !== '' && $parts[1] !== '0') {
                    $ids[(int)$parts[1]] = true;
                }
            }
        }
        $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $map = [];
        foreach (array_keys($ids) as $id_ubi) {
            $map[$id_ubi] = $repo->getArrayCasaPeriodos($id_ubi, $oIni, $oFin);
        }

        return $map;
    }
}
