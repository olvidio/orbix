<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Opciones (value => label) para <select> de centros (u_centros_dl).
 */
final class CentrosDropdown
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $filtro
     * @return array<int|string, string>
     */
    public function opciones(array $filtro = []): array
    {
        $sCondicion = $this->buildCondicion($filtro);

        return $this->centroDlRepository->getArrayCentros($sCondicion);
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

        if (!empty($filtro['tipo_ctr'])) {
            $tipoCtr = $filtro['tipo_ctr'];
            if (!is_string($tipoCtr)) {
                return empty($aWhere) ? '' : 'WHERE ' . implode(' AND ', $aWhere);
            }
            $regex = $this->tipoCtrRegex($tipoCtr);
            if ($regex !== null) {
                $aWhere[] = "tipo_ctr ~ '" . $regex . "'";
            }
        }

        return empty($aWhere) ? '' : 'WHERE ' . implode(' AND ', $aWhere);
    }

    private function tipoCtrRegex(string $key): ?string
    {
        return match ($key) {
            'seccion_no_s' => '^s[^s]',
            default => null,
        };
    }
}
