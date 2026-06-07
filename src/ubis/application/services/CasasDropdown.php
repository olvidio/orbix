<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Opciones (value => label) para <select> de casas (u_cdc).
 */
final class CasasDropdown
{
    public function __construct(
        private CasaDlRepositoryInterface $casaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $filtro
     * @return array<int|string, string>
     */
    public function opciones(array $filtro = []): array
    {
        $sCondicion = $this->buildCondicion($filtro);

        return $this->casaDlRepository->getArrayCasas($sCondicion);
    }

    /**
     * @param array<string, mixed> $filtro
     */
    private function buildCondicion(array $filtro): string
    {
        $aWhere = [];

        $soloActivas = !array_key_exists('active', $filtro) || (bool)$filtro['active'];
        if ($soloActivas) {
            $aWhere[] = "active = 't'";
        }

        if (!empty($filtro['sv'])) {
            $aWhere[] = "sv = 't'";
        }
        if (!empty($filtro['sf'])) {
            $aWhere[] = "sf = 't'";
        }

        if (!empty($filtro['id_ubi_in']) && is_array($filtro['id_ubi_in'])) {
            $ids = [];
            foreach ($filtro['id_ubi_in'] as $idRaw) {
                if (!is_int($idRaw) && !is_string($idRaw) && !is_float($idRaw) && !is_bool($idRaw) && $idRaw !== null) {
                    continue;
                }
                $id = (int) $idRaw;
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
            if (!empty($ids)) {
                $aWhere[] = 'id_ubi IN (' . implode(',', $ids) . ')';
            }
        }

        return empty($aWhere) ? '' : 'WHERE ' . implode(' AND ', $aWhere);
    }
}
