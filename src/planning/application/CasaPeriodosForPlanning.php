<?php

namespace src\planning\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

/**
 * Precarga periodos sf/sv/res por id_ubi para el planning de casas (claves u#…).
 */
final class CasaPeriodosForPlanning
{
    public function __construct(
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
    ) {
    }

    /**
     * @param array<int|string, array<string, list<array<string, mixed>>>> $a_actividades
     * @return array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>
     */
    public function collect(array $a_actividades, DateTimeLocal $oIni, DateTimeLocal $oFin): array
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
        $map = [];
        foreach (array_keys($ids) as $id_ubi) {
            $map[$id_ubi] = $this->casaPeriodoRepository->getArrayCasaPeriodos($id_ubi, $oIni, $oFin);
        }

        return $map;
    }
}
