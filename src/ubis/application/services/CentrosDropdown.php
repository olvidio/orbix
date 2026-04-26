<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Opciones (value => label) para <select> de centros (u_centros_dl).
 *
 * Reemplaza la firma legacy `setPosiblesCentros($sCondicion)` con SQL crudo
 * por un filtro estructurado whitelisted que se construye en backend.
 */
final class CentrosDropdown
{
    /**
     * Devuelve id_ubi => nombre_ubi aplicando el filtro recibido.
     *
     * Claves soportadas (todas opcionales):
     *   - 'active'    bool   — por defecto true. false para no filtrar por active
     *   - 'sv'        bool   — true => sv='t'
     *   - 'sf'        bool   — true => sf='t'
     *   - 'id_ubi_in' int[]  — restringe a esos id_ubi
     *   - 'tipo_ctr'  string — valor whitelisted, actualmente 'seccion_no_s' (regex ^s[^s])
     *
     * @param array<string, mixed> $filtro
     * @return array<int, string>
     */
    public static function opciones(array $filtro = []): array
    {
        $sCondicion = self::buildCondicion($filtro);
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

        return $repo->getArrayCentros($sCondicion);
    }

    /**
     * Traduce el filtro whitelisted a un fragmento WHERE seguro.
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

        if (!empty($filtro['tipo_ctr'])) {
            $regex = self::tipoCtrRegex((string)$filtro['tipo_ctr']);
            if ($regex !== null) {
                $aWhere[] = "tipo_ctr ~ '" . $regex . "'";
            }
        }

        return empty($aWhere) ? '' : 'WHERE ' . implode(' AND ', $aWhere);
    }

    /**
     * Whitelist de valores aceptados para el filtro tipo_ctr.
     * Los valores se traducen a patrones regex seguros (no input cliente).
     */
    private static function tipoCtrRegex(string $key): ?string
    {
        return match ($key) {
            'seccion_no_s' => '^s[^s]',
            default        => null,
        };
    }
}
