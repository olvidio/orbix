<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Opciones (value => label) para <select> de casas (u_cdc).
 *
 * Reemplaza la firma legacy `setPosiblesCasas($sCondicion)` con SQL crudo
 * por un filtro estructurado whitelisted que se construye en backend.
 */
final class CasasDropdown
{
    /**
     * Devuelve id_ubi => nombre_ubi aplicando el filtro recibido.
     *
     * Claves soportadas (todas opcionales):
     *   - 'active'    bool   — por defecto true. false para no filtrar por active
     *   - 'sv'        bool   — true => active='t' AND sv='t'
     *   - 'sf'        bool   — true => active='t' AND sf='t'
     *   - 'id_ubi_in' int[]  — restringe a esos id_ubi
     *
     * @param array<string, mixed> $filtro
     * @return array<int, string>
     */
    public static function opciones(array $filtro = []): array
    {
        $sCondicion = self::buildCondicion($filtro);
        $repo = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);

        return $repo->getArrayCasas($sCondicion);
    }

    /**
     * Traduce el filtro whitelisted a un fragmento WHERE seguro.
     * Todos los valores escalares se castean/validan antes de interpolar.
     *
     * @param array<string, mixed> $filtro
     */
    private static function buildCondicion(array $filtro): string
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
            $ids = array_values(array_filter(array_map('intval', $filtro['id_ubi_in']), static fn ($v) => $v > 0));
            if (!empty($ids)) {
                $aWhere[] = 'id_ubi IN (' . implode(',', $ids) . ')';
            }
        }

        return empty($aWhere) ? '' : 'WHERE ' . implode(' AND ', $aWhere);
    }
}
